FROM node:18-alpine

WORKDIR /app

COPY ./package.json ./package-lock.json ./
COPY ./node-server/chats_server ./
COPY ./.env ./

RUN npm install

ARG CHATS_PORT=8005
ENV CHATS_PORT=$CHATS_PORT

EXPOSE $CHATS_PORT

CMD ["node", "chats_server.cjs"]