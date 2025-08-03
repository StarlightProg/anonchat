FROM node:18-alpine

WORKDIR /app

COPY ./package.json ./package-lock.json ./
COPY ./node-server/anonymous ./
COPY ./.env ./

RUN npm install

ARG ANONYMOUS_PORT=8005
ENV ANONYMOUS_PORT=$ANONYMOUS_PORT

EXPOSE $ANONYMOUS_PORT

CMD ["node", "anonymous_chats_server.cjs"]