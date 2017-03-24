## DynamoDb configuration ##

Create the following environment variables in your .env file:

```
# AWS credentials
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=

# AWS SDK settings
AWS_SDK_ENDPOINT=
AWS_SDK_REGION=
AWS_SDK_VERSION=
AWS_SDK_HTTP_VERIFY=

# Multi tenancy
# Tables to be created in AWS with the following naming convention [NAMESPACE]-[DATABASE]-[RESOURCENAME]
# [RESOURCENAME] is to be set in the Schema classes
AWS_DYNAMODB_NAMESPACE=
AWS_DYNAMODB_DATABASE=
```

Example .evn configuration: [.env-example](../.env-example)

AWS SDK for PHP documentation: [https://aws.amazon.com/sdk-for-php/](https://aws.amazon.com/sdk-for-php/)
