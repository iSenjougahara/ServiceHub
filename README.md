<h2>Stack</h2>

<ul>
<li><b>Containerização:</b> Docker (executando dentro do WSL)</li>
<li><b>Queues:</b> Redis</li>
<li><b>Banco de dados:</b> MySQL</li>
<li><b>Backend:</b> PHP / Laravel</li>
<li><b>Testes:</b> Pest</li>
<li><b>Frontend:</b> Vue + Inertia</li>
</ul>

<hr>

<h2>Fluxo da aplicação</h2>

<p>A aplicação possui <b>três tipos de usuários</b>:</p>

<h3>Guest</h3>

<ul>
<li>Pode apenas <b>se cadastrar</b> na plataforma.</li>
</ul>

<h3>Manager</h3>

<ul>
<li>Pode <b>realizar login</b>.</li>
<li>Pode <b>criar tickets</b>.</li>
<li>Pode <b>visualizar apenas os tickets criados por ele</b>.</li>
<li>Ao criar um ticket, pode anexar um <b>arquivo JSON de retorno</b> contendo os dados que serão processados.</li>
</ul>

<h3>Technician</h3>

<ul>
<li>Pode <b>realizar login</b>.</li>
<li>Pode <b>visualizar tickets relacionados aos projetos vinculados ao seu perfil</b>.</li>
<li>Pode <b>alterar o status dos tickets</b> através da interface.</li>
</ul>

<hr>

<h2>Processamento de Tickets</h2>

<p>Quando um <b>manager cria um ticket</b>, ocorre o seguinte fluxo:</p>

<ol>
<li>O ticket é criado com status inicial <b>"aberto"</b>.</li>
<li>O arquivo <b>JSON anexado</b> é enviado para processamento através de uma <b>queue</b>.</li>
<li>Um <b>worker da queue</b> lê o arquivo JSON e monta os <b>detalhes do ticket</b>, populando o banco de dados.</li>
<li>Após o processamento:
<ul>
<li>Um <b>email é enviado</b> (registrado em <code>laravel.log</code>) contendo os dados do ticket.</li>
<li>O email é enviado para o <b>endereço vinculado ao projeto do ticket</b>.</li>
</ul>
</li>
<li>O <b>technician</b> é responsável por atualizar o status do ticket através da interface da aplicação.</li>
</ol>

<hr>

<h2>Execução dos testes</h2>

<p>Os testes são escritos utilizando <b>Pest</b>.</p>

<p>
Eles <b>devem ser executados dentro do ambiente Linux do WSL</b>, 
<b>fora do container Docker</b>.
</p>

<p><b>Exemplo:</b></p>

<pre>
user@123:~/projetos/ServiceHub$ vendor/bin/pest
</pre>

<p>
Atualmente os testes <b>não são executados dentro do container Docker</b>.
Seria possível configurar isso via <b>script/bash dentro do container</b>, 
porém <b>não foi implementado por limitação de tempo</b>.
</p>