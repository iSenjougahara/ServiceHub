stack:

containerização: Docker (inside wsl)
Queues: Redis
DB: mysql
app: php/laravel
testes: pest
frontend: vue/inertia


fluxo:

como technician voce pode logar, ver tickets relevantes aos projetos vinculados ao seu perfil, e alterar o status do mesmo.
como manager voce pode logar, ver tickets que somente voce mesmo criou e criar tickets com anexo de retorno (arquivo json).
como guest voce pode somente se cadastrar.

uma vez que o manager cria o ticket, a queue é responsavel por ler o arquivo json e montar os detalhes do ticket para população do banco, todos os tickets são criados por padrão em status "aberto" e cabe ao technician altera-lo de acordo via interface, é enviado tambem um email via laravel.logs com os dados do ticket ao endereço de email vinculado ao projeto do ticket.


testes PEST devem ser rodados na pasta do projeto dentro do ambiente linux wsl FORA do container docker, ex: 

user@123:~/projetos/ServiceHub$  vendor/bin/pest

eu acredito que conseguiria fazer funcionar via bash no container mas nao vou ter tempo.