ARG NGINX_VERSION
FROM nginx:${NGINX_VERSION} as base

# set timezone
ARG TZ=UTC
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN rm -rf /etc/nginx/conf.d/default.conf

# workdir
WORKDIR /etc/nginx/

CMD ["nginx"]



FROM base as local

# nginx config
COPY docker/nginx/nginx.conf /etc/nginx/
COPY docker/nginx/conf.d/* /etc/nginx/conf.d/

# cleanup
COPY docker/shared/scripts/cleanup.sh /tmp/scripts/cleanup.sh
RUN /tmp/scripts/cleanup.sh
