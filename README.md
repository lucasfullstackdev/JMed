
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1 align="center">JMed</h1>
<h4 align="center">Solucionando um problema real com Laravel</h4>

## Por que este projeto?
- Este projeto foi criado simulando um cenário onde uma clínica precisava de todas as mais de 8 mil bulas, para posterior alimentação de um site feito em WordPress. 
- A clínica havia chamado outros desenvolvedores, porém como a API da Anvisa apresenta graves problemas de instabilidade e eficiência, a obtenção dos PDF's em tempo real era algo inviável.

## Sobre o projeto
Fazer o download de todas as mais de 8 mill bulas disponíveis.

Pontos abordados:
 - Command
 - Jobs
 - Processamento em lote
 - Requisições HTTP
 - Eloquent ORM
 - POO
 - Modelagem do Banco de Dados

## Cenário

**Total de Bulas Disponíveis:** 8.276  
**Número de Categorias:** 11

#### Problemas Identificados:
- **Instabilidade da API:** A API apresenta um elevado grau de instabilidade, impactando a consistência das respostas e a eficiência das requisições.
- **Ausência de Categoria no JSON:** O JSON de resposta não inclui a categoria da bula, o que dificulta a categorização direta a partir dos dados fornecidos.
- **Falta de Link para PDF no JSON:** O JSON de resposta não contém um link direto para o PDF da bula, exigindo uma requisição adicional para obter o arquivo.
- **Validade do Token para PDF:** O token necessário para acessar o PDF só pode ser utilizado dentro de um intervalo aleatório entre 1 e 15 minutos, inviabilizando seu uso imediato após a geração.


# Solução do Problema

- Estudar a API interna da Anvisa com o objetivo de entender qual o maior lote possível de ser consultado.
- Realizar consultas em lotes (manualmente) por categoria para que possamos fazer a categorização manual (uma vez que o JSON não retorna uma propriedade que faça menção à categoria).
- Armazenar todos os medicamentos em um JSON.
- Uma vez que temos todos os medicamentos armazenados em JSON, precisaremos criar um Command para realizar a carga desses dados para o banco de dados.

## Processos Após Armazenamento no Banco de Dados
- Uma vez que temos todos os medicamentos armazenados no banco de dados:
  - Foi criado um Command para que a operação a seguir pudesse ser realizada.
  - Para cada registro foi necessário:
    - Despachar um Job para consultar novamente o medicamento, gerando um novo token.
    - Uma vez que tenhamos o token, despachar um segundo Job em uma segunda fila responsável por obter o PDF e convertê-lo para base64.
      * Aqui uma coisa estranha aconteceu. Por algum motivo, a API da Anvisa determina (aleatoriamente) um intervalo mínimo que pode variar de 1 a 15 minutos, e o token só poderia ser utilizado após este intervalo. Quando isso acontecia, eu fazia um regex no texto retornado para poder fazer um cálculo e assim tentar novamente após o intervalo indicado.
    - Com o PDF em base64, despachar um terceiro Job em uma terceira fila, responsável por salvar o PDF no banco de dados.

## Conclusão do Processo
- Uma vez que todos os 8.276 registros estivessem com o respectivo PDF:
  - Foi criado um Command responsável por despachar um Job para cada um dos registros, com a única finalidade de fazer o download dos arquivos. Logo em seguida, um segundo Job era despachado para que posteriormente fosse possível atualizar uma nova coluna "downloaded" como true, para termos o controle de quais arquivos tinham sido salvos com sucesso.

### Observações Importantes

- Para o processo de geração de token e obtenção do PDF, devido ao volume de requisições envolvidas (no mínimo 16.552) e visando não onerar os recursos da API da Anvisa, foi estipulado um intervalo de 7 segundos para cada um dos registros no banco de dados. Pode-se dizer então que a obtenção de PDF era realizada a cada 7 segundos.
- Devido ao alto volume de arquivos que deveriam ser baixados, e levando em consideração as limitações da minha máquina, estipulei um intervalo de 10 segundos entre cada download.

## Dados Observados
- A carga de dados foi otimizada de 4 horas para 9 segundos.
- O processo de obtenção dos PDFs demorou 21 horas.
- O processo de download dos PDFs demorou 11 horas.

## Considerações finais
- Esta API não representa uma amostra real, devendo ser utilizada apenas para se ter uma noção sobre como funciona um API REST.
- Qualquer dúvida ou sugestão, entre em contato pelo e-mail: contato@jellycode.com.br
