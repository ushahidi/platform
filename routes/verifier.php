<?php
if (getenv('APP_ENV') === 'local' || getenv('APP_ENV') === 'dev') {
    resource($router, 'verifier/db', 'VerifyController@db');
    resource($router, 'verifier/env', 'VerifyController@conf');
}
