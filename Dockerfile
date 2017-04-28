FROM fazy/apache-symfony
RUN apt-get update \
    && apt-get -yq install php5-pgsql
ADD . /app
