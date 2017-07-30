npm install -g serverless

serverless install --url https://github.com/pmuens/serverless-crud
cd serverless-crud
npm install

今回は `us-east-1` を `ap-northeast-1` 全置換しました。

plugins:
  - serverless-offline
  - serverless-dynamodb-local

custom:
  dynamodb:
    start:
      port: 8000
      inMemory: true
      migrate: true
      seed: true
    seed:
      localhost:
        sources:
          - table: todos
            sources: [./todos.json]
```
```
npm install serverless-offline --save-dev
npm install --save serverless-dynamodb-local
sls dynamodb install
```
```
serverless offline start

new AWS.DynamoDB.DocumentClient({
    region: 'localhost',
    endpoint: 'http://localhost:8000'
})
