FROM chrif/php

COPY php.ini /usr/local/etc/php/php.ini

ENV ENV="/root/.ashrc"

RUN echo 'PATH=/opt/project/bin:$PATH' >> /root/.ashrc

WORKDIR /opt/project
