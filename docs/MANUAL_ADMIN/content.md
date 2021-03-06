# Manual do Administrador

Este manual serve para o administrador do SiGE entender como funciona as telas do administrador.
Pretendemos mostrar cada tela e identificar sua função.

Um administrador e um usuário comum são diferenciados apenas por uma coluna no banco
de dados, na tabela `pessoa` a coluna `administrador` com o valor `true`.

É possível tornar qualquer usuário administrador através do SiGE, como veremos em
\ref{permissao-usuario}.

Ao se logar o administrador ver a mesma página do usuário com a diferença que
no menu principal aparece o item **Admin**.

### Inscrições \label{inscricoes}

Mapeada como: `/admin ou /inscricoes [admin/ParticipanteController.index]`

Ao clicar no item **Admin** do menu principal você é levado para a página de
incrições. Aqui é onde você, no dia do evento, faz a confirmação dos participantes
que se inscreveram previamente. Com isso você confirma que aquele participante
realmente esteve no Encontro e assim o certificado dele fica habilitado para
download.

Na lista aparecerão somente os 20 primeiros cadastrados no Encontro. Então é preferível
que você utilize a busca, para ter resultado precisos.

Ao encontrar a pessoa basta clicar em **Confirmar**. Caso tenha se engadado clique em
**Desfazer**. Tudo é feito sem que a página seja recarregada, basta esperar que a palavra
Confirmar se torne Desfazer. Além disso uma mensagem vai aparecer confirmando o pedido.

Ou seja, essa é a página principal no dia do Encontro, então para as pessoas
da recepção essa é a página que você deve mostrar e ensinar os macetes para
um cadastramento rápido, evitanto filas muito grandes.

#### DICA 1:

Ao chegar escolas ou instituições utilize a Busca por Instituição, como ela traz
até 20 pessoas é bem provável que venham todos os alunos e poupa seu trabalho
de pesquisar nome por nome.

#### DICA 2:

Faça o mesmo para a Caravana, busque pela Caravana e tenha todos os participantes
daquela caravana na lista.

### Caravana \label{caravana}

Mapeada como: `/admin/caravana [admin/CaravanaController.index]`

Nessa tela é possível ver as caravanas que desejam participar do evento, com os dados
da caravana, responsável, quantidade de homens e mulheres.

Nessa tela ainda existe o botão validar e invalidar, mas na realidade o melhor
é conversar com o responsável e definir com ele os detalhes da vinda da
caravana. Essa tela deve servir mesmo como controle, para você administrador
junto com sua equipe decidir se é possível ou não a vinda da caravana.

### Evento \label{evento}

Mapeada como: `/admin/evento [admin/EventoController.index]`

Essa tela é essencial principalmente antes e depois do encontro.

#### DEFINIÇÃO:

Definimos **Encontro** com sendo um conjunto de **Eventos**, onde os **Eventos**
são definidos como **Palestras**, **Mini-cursos** ou **Oficinas**.

\bigskip

Antes do encontro, essa é a tela onde você irá acompanhar a submisão de trabalhos.
Aqui você sabe os dados do evento, junto com um link para os detalhes do
evento \ref{detalhes-evento}.

### Detalhes do Evento \label{detalhes-evento}

Mapeada como: `/admin/evento/detalhes/id/:id [admin/EventoController.detalhes]`

Nessa tela além dos dados mostrados na tela de Eventos \ref{evento}, você
pode ver um Resumo, Preferência de Horário e as Tecnologias envolvidas.
Esse último item é muito útil pois ele diz se o usuário precisará de ferramentas
ou aparelhos específicos para sua palestra. Exemplo: Internet na sala,
Entrada para HDMI, Aparelho de Som, plugin do VLC, Ambiente Python, enfim.

Logo abaixo você tem a lista de horários, onde é possível Criar um horário,
Editar \ref{salvar-horario} ou Deletar. Esse trecho define os horários inicial e final do evento
além da sala e data que acontecerá.

Mais abaixo tem a lista de outros palestrantes.

Após criar os horários é hora de validar o evento. Para isso basta clicar em **Validar**
no sub menu. Também é possível editar os dados do evento clicando em **Editar**.

Ao final do evento é preciso que você confirme quem realmente palestrou. Para isso basta
clicar em **Apresentado** no sub menu. Isso faz com que o certificado de palestrante
apareça para o participante.

### Criar/Editar Horário \label{salvar-horario}

Mapeada como: `/admin/horario/criar/evento/:evento [admin/HorarioController.criar]`

`/admin/horario/editar/evento/:evento/id/:id [admin/HorarioController.editar]`

Tela para adicionar ou editar o horário do evento, cada evento pode ter um ou
mais horários. Algumas oficinas e mini-cursos às vezes levam mais de um
dia para serem realizados. Ou uma palestra pode ser muito requisitada
e pode ser apresentada mais de uma vez.

Caso haja choque de horário na mesma sala, no mesmo dia, o SiGE te avisa.

Logo abaixo aparece os horários já cadastrados.

Os horários são de uma em uma hora, ao selecionar o horário inicial o horário
de término é atualizado para uma hora a mais.

#### OBSERVAÇÃO:

A configuração do intervalo de horas e a hora mínima e máxima atualmente
estão no código-fonte, ou seja, para modificar seu comportamento
é preciso alterar o código. Em uma outra versão do SiGE isso pode mudar.
Mas enquanto isso o código-fonte para essa mudança pode ser encontrado em:
`application/forms/Horarios.php` na linha `77`.

### Relatórios

Essa tela é auto explicativa :)

### Configurações

Nessa tela tem o Gerenciamento dos Encontros \ref{gerenciar-encontro}
e Permissão de usuários \ref{permissao-usuario}.

### Gerenciamento dos Encontros \label{gerenciar-encontro}

Para criar o primeiro encontro é preciso usar diretamente o banco de dados,
mas nos encontros seguintes use o SiGE. Nessa tela é possível ver a lista
de encontros, Criar ou Editar encontro, Editar mensagens de e-mail.

### Permissão de usuários \label{permissao-usuario}

Essa tela serve para procurar participantes e torná-los administradores ou
usuários comuns, e ainda definir se ele é da Organização ou Coordenação do
encontro. Essa última opção ainda não influencia no sistema, mas para
as próximas versões podemos usar essa informação.
