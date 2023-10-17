drop database if exists agenda;
create database if not exists agenda;
use agenda;

create table if not exists usuario (
	id integer primary key not null auto_increment,
	nome varchar(255) not null,
	email varchar(255) not null,
	senha varchar(255) not null,
	foto varchar(255)
);

create table if not exists usuario_token (
	id integer primary key not null auto_increment,
  id_usuario integer not null,
	token varchar(255) not null
);

-- ÍNDICES

alter table usuario add index (email, senha);

-- FOREIGN KEY

alter table usuario_token add foreign key (id_usuario) references usuario(id);