ARG NODEJS_VERSION
FROM node:${NODEJS_VERSION}

ARG NODEJS_PACKAGE_URL

RUN npm config set registry ${NODEJS_PACKAGE_URL}

RUN mkdir -p /usr/local/app

ENV NODE_ENV dev

WORKDIR /usr/local/app