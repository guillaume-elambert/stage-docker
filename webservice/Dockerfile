FROM tomcat

LABEL org.opencontainers.image.authors="Guillaume ELAMBERT"

EXPOSE 8080
COPY *.war webapps/

ENTRYPOINT ["catalina.sh", "run"]
CMD echo Tomcat running...