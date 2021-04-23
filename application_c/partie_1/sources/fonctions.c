#include "../headers/fonctions.h"
#include <curl/curl.h>


int downloadXML(char* url, char* fileName){
	CURL *curl;
	CURLcode res;
	FILE *fd;

	struct curl_slist *header = NULL;

    /* try to open/create the file where to download */
	fd = fopen(fileName, "wb");
	if(!fd)
		return 0;

	curl = curl_easy_init();

	if(curl) {

	/* setting the headers of the request */	
		curl_slist_append(header, "Accept: application/xml, application/json");
    	//curl_slist_append(header, "Content-Type: application/json");
    	curl_slist_append(header, "charset: utf-8");


    /* setting the origin of the download */
		curl_easy_setopt(curl, CURLOPT_URL, url);
	/* adding the headers */
		curl_easy_setopt(curl, CURLOPT_HTTPHEADER, header);
    /* setting where to write the downloaded datas */
		curl_easy_setopt(curl, CURLOPT_WRITEDATA, fd);

	/* in case of authentication / could be setup in the header (ex: bearer)
		curl_easy_setopt(curl, CURLOPT_USERNAME, "login");
		curl_easy_setopt(curl, CURLOPT_PASSWORD, "password");
	*/

    /* perfoming the request */
		res = curl_easy_perform(curl);

    /* checking for errors */
		if(res != CURLE_OK){
			fprintf(stderr, "curl_easy_perform() failed: %s\n", curl_easy_strerror(res));
			fclose(fd);
			return 0;
		}
    /* always cleanup */
		curl_easy_cleanup(curl);
	}
	curl_global_cleanup();
	fclose(fd);

	return 1;
}


int sendXML(char* url, char* fileName){
	CURL *curl;
	CURLcode res;
	FILE *fd;

	/* try to open/create the file where to download */
	fd = fopen(fileName, "wb");
	if(!fd)
		return 0;

	curl = curl_easy_init();

	if(curl) {
		/* setting the destination of the upload */
	    curl_easy_setopt(curl, CURLOPT_URL, url);

	    /* tell it to "upload" to the URL */
	    curl_easy_setopt(curl, CURLOPT_UPLOAD, 1L);

	    /* setting where to read the uploaded data */
	    curl_easy_setopt(curl, CURLOPT_READDATA, fd);


	    /* perfoming the request */
		res = curl_easy_perform(curl);

	    /* checking for errors */
			if(res != CURLE_OK){
				fprintf(stderr, "curl_easy_perform() failed: %s\n", curl_easy_strerror(res));
				fclose(fd);
				return 0;
			}
	    /* always cleanup */
		curl_easy_cleanup(curl);
	}
	curl_global_cleanup();
	fclose(fd);

	return 1;
}