CREATE TABLE tudoehvendasdb.dbo.cliente (
	id INT IDENTITY(1, 1) PRIMARY KEY,
	nome varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	criado_em datetime2 DEFAULT getdate() NULL,
	inativo smallint DEFAULT 0 NULL
);

CREATE TABLE tudoehvendasdb.dbo.procedimento (
	id INT IDENTITY(1, 1) PRIMARY KEY,
	descricao varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	criado_em datetime2 DEFAULT getdate() NULL,
	inativo smallint DEFAULT 0 NULL
);

CREATE TABLE tudoehvendasdb.dbo.lancamento (
	id INT IDENTITY(1, 1) PRIMARY KEY,
	sequencia INT,
	cliente_id int NULL,
    apurado_em datetime2 DEFAULT getdate() NULL,
	observacao varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	lancado_em datetime2 DEFAULT getdate() NULL,
	excluido smallint DEFAULT 0 NULL
);

CREATE TABLE tudoehvendasdb.dbo.lancamento_detalhe (
	id INT IDENTITY(1, 1) PRIMARY KEY,
	lancamento_id int NOT NULL,
    procedimento_id int NOT NULL,
    valor smallint DEFAULT 0 NOT NULL,
	observacao varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	lancado_em datetime2 DEFAULT getdate() NULL,
	excluido smallint DEFAULT 0 NULL
);

CREATE TABLE tudoehvendasdb.dbo.usuario (
	id INT IDENTITY(1, 1) PRIMARY KEY,
	nome varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	email varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	senha varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	criado_em datetime2 DEFAULT getdate() NULL,
	admin smallint DEFAULT 0 NOT NULL,
	cliente_id int NULL,
    inativo smallint DEFAULT 0 NULL,
	CONSTRAINT usuario_email_uk UNIQUE (email),
	CONSTRAINT usuario_cliente_fk FOREIGN KEY (cliente_id) REFERENCES tudoehvendasdb.dbo.cliente(id)
);

insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Responsável direto pelas vendas' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Departamento de vendas/vendedor' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Metas de faturamento' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Meta de vendas de produtos adicionais' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Check-list de entrada' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Orçamentos via sistema' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Gestão de orçamentos' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Venda de produtos adicionais' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Pós-venda' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Cadastro de clientes via sistema' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Site' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Redes Sociais (postagens frequentes)' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Tráfego pago (insta, google, face)' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Canais de divulgação da Oficina' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Parcerias para indicação (comércio local)' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Programa de indicação (clientes da oficina)' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Realização de Eventos' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Sala de espera para clientes' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Decoração e ornamentos  da oficina' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Produtos expostos no balcão ou expositores' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Programa de fidelidade' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Serviço de leva e trás' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Placa com promoção em frente a oficina' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Controle e gestão de clientes para troca de óleo' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Box para serviços rápidos' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Treinamento da equipe/Reunião' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Parcerias com outras oficinas/retíficas/torno/lanternagem' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Cadastro no Google Meu Negócio' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Condições de pagamentos' );
insert into tudoehvendasdb.dbo.procedimento (descricao) values ('Fachada da empresa com principais serviços' );

insert into tudoehvendasdb.dbo.cliente (nome) values ('Cliente Demonstração' );

insert into usuario (nome, email, senha, admin, cliente_id, criado_em) values ('ADM', 'gabrielboegeholz@gmail.com', '$2y$10$GN8bYo70ySOG/j.1Mweps.Y8bO8NwBDJkXSYp0sscb4yhiMl2os96', 1, null, '2025-09-09 14:00:00');