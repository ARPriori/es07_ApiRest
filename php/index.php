<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/AlunniController.php';

$app = AppFactory::create();

//ALUNNI
$app->get('/alunni', "AlunniController:index");
$app->get('/alunni/{id}', "AlunniController:show");
$app->post('/alunni', "AlunniController:create");
$app->put('/alunni/{id}', "AlunniController:update");
$app->delete('/alunni/{id}', "AlunniController:destroy");

//CERTIFICAZIONI
$app->get('/cert', "CertificazioniController:index");
$app->get('/cert/{id}', "CertificazioniController:show");
$app->post('/cert', "CertificazioniController:create");
$app->put('/cert/{id}', "CertificazioniController:update");
$app->delete('/cert/{id}', "CertificazioniController:destroy");

//ALUNNI - CERT
$app->get('/alunni/{id}/cert', "CertNestedController:index");
$app->get('/alunni/{id}/cert/{cert_id}', "CertNestedController:show");
$app->post('/alunni/{id}/cert', "CertNestedController:create");
$app->put('/alunni/{id}/cert/{cert_id}', "CertNestedController:update");
$app->delete('/alunni/{id}/cert/{cert_id}', "CertNestedController:destroy");

$app->run();
