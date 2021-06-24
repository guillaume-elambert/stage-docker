FROM openjdk
LABEL org.opencontainers.image.authors="Guillaume ELAMBERT"

# Expose web port
EXPOSE 8080

# Tomcat Version
ENV TOMCAT_VERSION_MAJOR 10
ENV TOMCAT_VERSION_FULL  10.0.7


# Set environment
ENV CATALINA_HOME /opt/tomcat


# Download and install
RUN curl -LO "https://archive.apache.org/dist/tomcat/tomcat-${TOMCAT_VERSION_MAJOR}/v${TOMCAT_VERSION_FULL}/bin/apache-tomcat-${TOMCAT_VERSION_FULL}.tar.gz" &&\
    gunzip apache-tomcat-${TOMCAT_VERSION_FULL}.tar.gz &&\
    tar -xf apache-tomcat-${TOMCAT_VERSION_FULL}.tar -C /opt &&\
    rm apache-tomcat-${TOMCAT_VERSION_FULL}.tar &&\
    ln -s /opt/apache-tomcat-${TOMCAT_VERSION_FULL} ${CATALINA_HOME} &&\
    rm -rf ${CATALINA_HOME}/webapps/examples ${CATALINA_HOME}/webapps/docs

# Configuration
ADD tomcat-users.xml ${CATALINA_HOME}/conf/
RUN sed -i 's/52428800/5242880000/g' ${CATALINA_HOME}/webapps/manager/WEB-INF/web.xml 

COPY *.war $CATALINA_HOME/webapps/

# Launch Tomcat on startup
CMD ${CATALINA_HOME}/bin/catalina.sh run

WORKDIR ${CATALINA_HOME}