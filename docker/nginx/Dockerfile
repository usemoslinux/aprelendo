FROM nginx

RUN openssl req -x509 -nodes -days 365 \
    -subj  "/C=CA/ST=ON/O=Aprelendo/CN=aprelendo.com" \
    -newkey rsa:2048 -keyout /etc/nginx/cert.key \
    -out /etc/nginx/cert.pem;
