/* The port number is passed as an argument */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <sys/wait.h>
#include <time.h>
#include <sys/stat.h>
#include <arpa/inet.h>
#include <mysql/mysql.h>

void error(const char *msg)
{
    perror(msg);
    exit(1);
}

int main(int argc, char *argv[])
{
    int sockfd, newsockfd, portno;
    socklen_t clilen;
    char buffer[1024];
    struct sockaddr_in serv_addr, cli_addr;
    int n;
    if (argc < 2) {
        fprintf(stderr,"BLAD, NUMER PORTU NIE ZOSTAL PODANY\n");
        exit(1);
    }
    // create a socket
    // socket(int domain, int type, int protocol)
    sockfd =  socket(AF_INET, SOCK_STREAM, 0);
    if (sockfd < 0)
        error("BLAD PODCZAS OTWIERANIA SOCKETU\n");

    // clear address structure
    bzero((char *) &serv_addr, sizeof(serv_addr));

    portno = atoi(argv[1]);

    /* setup the host_addr structure for use in bind call */
    // server byte order
    serv_addr.sin_family = AF_INET;

    // automatically be filled with current host's IP address
    serv_addr.sin_addr.s_addr = INADDR_ANY;

    // convert short integer value for port must be converted into network byte order
    serv_addr.sin_port = htons(portno);

    // bind(int fd, struct sockaddr *local_addr, socklen_t addr_length)
    // bind() passes file descriptor, the address structure,
    // and the length of the address structure
    // This bind() call will bind  the socket to the current IP address on port, portno
    if (bind(sockfd, (struct sockaddr *) &serv_addr,
             sizeof(serv_addr)) < 0)
        error("BLAD - bind()");

    // This listen() call tells the socket to listen to the incoming connections.
    // The listen() function places all incoming connection into a backlog queue
    // until accept() call accepts the connection.
    // Here, we set the maximum size for the backlog queue to 5.
    listen(sockfd,5);

    // The accept() call actually accepts an incoming connection
    clilen = sizeof(cli_addr);

    // This accept() function will write the connecting client's address info
    // into the the address structure and the size of that structure is clilen.
    // The accept() returns a new socket file descriptor for the accepted connection.
    // So, the original socket file descriptor can continue to be used
    // for accepting new connections while the new socker file descriptor is used for
    // communicating with the connected client.
    while(1)
    {
        newsockfd = accept(sockfd,
                           (struct sockaddr *) &cli_addr, &clilen);
        if (newsockfd < 0)
            error("BLAD - accept()");

        printf("server: got connection from %s port %d\n",
               inet_ntoa(cli_addr.sin_addr), ntohs(cli_addr.sin_port));


		/* FAZA ODBIORU DANYCH: ID SKRYPTU, ID UZYTKOWNIKA, LOGIN UZYTKOWNIKA */

        bzero(buffer,256);

        int script_id;
        int user_id;
        int strlen;

        n = read(newsockfd,&script_id, sizeof(int));
        if (n < 0) error("Blad podczas czytania z socketu // scriptid");
        printf("SCRIPT ID: %d\n", script_id);

        n = read(newsockfd,&strlen, sizeof(int));
        if (n < 0) error("Blad podczas czytania z socketu // Login string length");

        char login[512];
        memset(login, 0, 512);

        n = read(newsockfd, login, strlen);
        if (n != strlen) error("Blad podczas czytania z socketu // Login string");
        printf("LOGIN: %s\n", login);

        char nazwaP[2048];
        memset(nazwaP, 0, 2048);

        n = read(newsockfd,&strlen, sizeof(int));
        if (n < 0) error("Blad podczas czytania z socketu // Filename string length");

        n = read(newsockfd,nazwaP, strlen);
        if (n < 0) error("Blad podczas czytania z socketu // Filename string");
        printf("NAZWA PLIKU: %s\n", nazwaP);

        /* FAZA POBRANIA DANYCH SKRYPTU, ZAPISANIA DO PLIKU */

        MYSQL *conn;
        MYSQL_RES *res;
        MYSQL_ROW row;
        FILE *file;
        conn = mysql_init(NULL);
        //PRZEDOSTATNI PARAMETR DO ZMIANY NA NULL PODCZAS ODPALANIA POZA LOCALHOSTEM ALANA
		/* 
		if (!mysql_real_connect(conn, "localhost",
                                "root", "root", "robozone", 0, "/Applications/MAMP/tmp/mysql/mysql.sock", 0)) {
            fprintf(stderr, "%s\n", mysql_error(conn));
            exit(1);
        }
		*/
		
        if (!mysql_real_connect(conn, "mysql.wmi.amu.edu.pl",
                                "robozone", "ereemellienters", "robozone", 0, NULL, 0)) {
            fprintf(stderr, "%s\n", mysql_error(conn));
            exit(1);
        }

		char filebuffer[1024*1024];
        char queryString[2048];
		char polecenie[2048];
        char filename[256];
		char datetime[19];
		char ip[15];
		char script_id_str[12];
        pid_t child_pid;
        int status;

        memset(queryString, 0, 2048);
        memset(filename,0, 256);

        sprintf(queryString, "SELECT `script_data`, `script_size` FROM `Scripts` where `script_id` =  '%d';", script_id);

        if (mysql_query(conn, queryString)) {
            fprintf(stderr, "BLAD PODCZAS SELECT DANYCH PLIKU: %s\n", mysql_error(conn));
            exit(1);
        }

        printf("Polaczono z baza\n");

        res = mysql_store_result(conn);
        row = mysql_fetch_row(res);

        file = fopen(nazwaP, "wb");

        unsigned long *lengths = mysql_fetch_lengths(res);

        n = fwrite(row[0], lengths[0], 1, file);

        int compare = atoi(row[1]);

        if (ferror(file)  && n!=compare)
        {
            fprintf(stderr, "Blad podczas zapisywania do pliku!\n");
            exit(1);
        }

		mysql_free_result(res);

        printf("ZAPISANO DANE DO PLIKU: %s\n", nazwaP);

        chmod(nazwaP, S_IRWXU|S_IRGRP|S_IXGRP|S_IROTH);
        fclose(file);
		
        memset(polecenie,0, 2048);
        sprintf(polecenie,"./%s", nazwaP);
		
		/* FAZA PRZYGOTOWANIA DANYCH, POTRZEBNYCH DO WYKONANIA INSERTA DO TABELI HISTORY */
		
        time_t t = time(0);
        struct tm *now = localtime(&t);

        sprintf(datetime, "%d-%d-%d %d:%d:%d", now->tm_year+1900, now->tm_mon+1, now->tm_mday, now->tm_hour, now->tm_min, now->tm_sec);

        memset(queryString, 0, 2048);

        sprintf(queryString, "SELECT `user_id` FROM `Users` where `login` = '%s';", login);

        if (mysql_query(conn, queryString)) {
            fprintf(stderr, "BLAD PODCZAS SELECT USER ID: %s\n", mysql_error(conn));
            exit(1);
        }

        res = mysql_store_result(conn);

        row = mysql_fetch_row(res);

        user_id = atoi(row[0]);
        printf("USER ID: %d\n", user_id);

        memset(ip, 0, 15);
        sprintf(ip, "%s", inet_ntoa(cli_addr.sin_addr));
		  
		/* FAZA SPRAWDZENIA POPRAWNOSCI SKRYPTU */
		
        if( (child_pid=fork()) == 0 ){
            execlp("python", "python", "-m", "py_compile", polecenie, NULL);
        }
        else{
            waitpid(child_pid,&status,0);
        }
		
		char filename2[2048];
		
		strcpy(filename2, nazwaP);
		
		strcat(filename2, "c");
		
		file = fopen(filename2, "rt");
		
		int r;
		
		if( file == NULL ) { 
			printf("Błąd podczas kompilacji.\n");
			memset(queryString, 0, 2048);

	        sprintf(queryString, "INSERT INTO `History`(`u_id`, `s_id`, `exec_date`, `log`, `ip`) VALUES('%d', '%d', '%s', '%s', '%s')", user_id, script_id, datetime, "BŁĄD KOMPILACJI!", ip);

	        if (mysql_query(conn, queryString)) {
	            fprintf(stderr, "BLAD PODCZAS INSERT: %s\n", mysql_error(conn));
	            exit(1);
	        }
	        else{
	            printf("INSERT ZAKONCZYL SIE POWODZENIEM\n");
				printf("ZAKONCZONO OBSLUGE: %s Z BLEDEM\n", inet_ntoa(cli_addr.sin_addr));
	        }
			fclose(file);
	        mysql_close(conn);
			close(newsockfd);
			continue;
		}
		else {
			printf("Kompilacja skryptu zakonczona powodzeniem\n");
			fclose(file);
	        if( (child_pid=fork()) == 0 ){
	            execlp("rm", "rm", filename2, NULL);
	        }
	        else{
	            waitpid(child_pid,&status,0);
	        }
		}

		/* FAZA URUCHOMIENIA SKRYPTU */


        if( (child_pid=fork()) == 0 ){
            sprintf(script_id_str, "%d", script_id);
            execlp("sudo", "sudo", polecenie, script_id_str , NULL);
        }
        else{
            waitpid(child_pid,&status,0);
        }

		/* FAZA ODESLANIA WYNIKU DO BAZY DANYCH */

        memset(filename,0, 256);
        sprintf(filename, "%d.txt", script_id);
        file = fopen(filename, "rt");

        if(file){
            n = fread(filebuffer,1, sizeof(filebuffer), file);
            if(n < 0) { error("Blad podczas czytania z pliku!"); exit(1); }
        }

        fclose(file);

		memset(queryString, 0, 2048);

        sprintf(queryString, "INSERT INTO `History`(`u_id`, `s_id`, `exec_date`, `log`, `ip`) VALUES('%d', '%d', '%s', '%s', '%s')", user_id, script_id, datetime, filebuffer, ip);

        if (mysql_query(conn, queryString)) {
            fprintf(stderr, "BLAD PODCZAS INSERT: %s\n", mysql_error(conn));
            exit(1);
        }
        else{
            printf("INSERT ZAKONCZYL SIE POWODZENIEM\n");
        }
        mysql_close(conn);

        close(newsockfd);
		
        if( (child_pid=fork()) == 0 ){
			memset(filename,0,256);
            sprintf(filename, "%d.txt", script_id);
            execlp("rm", "rm", filename, NULL);
        }
        else{
            waitpid(child_pid,&status,0);
        }


        if( (child_pid=fork()) == 0 ){
            execlp("rm", "rm", nazwaP, NULL);
        }
        else{
            waitpid(child_pid,&status,0);
        }
		
		
        printf("ZAKONCZONO OBSLUGE: %s\n",inet_ntoa(cli_addr.sin_addr));
    }
    close(sockfd);
    return 0;
}
