'use strict';

module.exports = (event, dynamoDb, callback) => {
  const params = {
    TableName: 'todos',
  };
  function getData(params) {
    return new Promise((resolve, reject) => {
      dynamoDb.scan(params, (error, data) => {
        resolve(data);
      });
    });
  }

  let result = {};
  (async () => {
    result = await getData(params);
  })().then(() => {
    let body = "<html><head>";
    body += "<link rel='stylesheet' type='text/css' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>";
    body += "<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>";
    body += "</head><body style='margin:18px'>";
    body += "<h2 class='sub-header'>todos</h2>";
    body += "<table class='table table-striped'>";
    for(var i = 0;i < result.Items.length;i++){
      body += "<tr>";
      body += "<td>" + result.Items[i].id + "</td>";
      body += "<td>" + result.Items[i].column1 + "</td>";
      body += "<td>" + result.Items[i].column2 + "</td>";
      body += "</tr>";
    }
    body += "</table></body></html>";
    return callback(null, body);
  }).catch((err) => {
    return callback(err);
  })
};
