'use strict';

module.exports = (event, dynamoDb, callback) => {
  const params = {
    TableName: 'todos',
  };

  return dynamoDb.scan(params, (error, data) => {
    if (error) {
      callback(error);
    }
    callback(error, data.Items);
  });
};
