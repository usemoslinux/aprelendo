FROM nginx

RUN openssl req -x509 -nodes -days 365 \
    -subj  "/C=CA/ST=QC/O=Company Inc/CN=bee" \
    -newkey rsa:2048 -keyout /etc/nginx/cert.key \
    -out /etc/nginx/cert.pem;
