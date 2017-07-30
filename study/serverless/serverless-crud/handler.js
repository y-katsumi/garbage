'use strict';

const actionList = [
  { name: 'create', action: require('./todos-create.js') }
  ,{ name: 'readAll', action: require('./todos-read-all.js') }
  ,{ name: 'readOne', action: require('./todos-read-one.js') }
  ,{ name: 'update', action: require('./todos-update.js') }
  ,{ name: 'delete', action: require('./todos-delete.js') }
  ,{ name: 'showAll', action: require('./todos-show-all.js') }
];

const AWS = require('aws-sdk');
const dynamoDb = new AWS.DynamoDB.DocumentClient({
    region: 'localhost',
    endpoint: 'http://localhost:8000'
})

for(let i = 0; i < actionList.length; i++){
  module.exports[actionList[i].name] = (event, context, callback) => {
    actionList[i].action(event, dynamoDb, (error, result) => {
      if (error) {
        const response = {
          statusCode: 204,
          headers: {
            "Access-Control-Allow-Origin" : "*"
          },
          body: JSON.stringify(error),
        };

        context.succeed(response);
      }
      if (typeof result == "string") {
        const response = {
          statusCode: 200,
          headers: {
            "Access-Control-Allow-Origin" : "*"
            ,"Content-Type": "text/html"
          },
          body: result
        };
        context.succeed(response);
      } else {
        const response = {
          statusCode: 200,
          headers: {
            "Access-Control-Allow-Origin" : "*"
          },
          body: JSON.stringify(result)
        };
        context.succeed(response);
      }
    });
  };
}
