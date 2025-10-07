-- desafio 1
select ID_VENDEDOR as id,
       NOME        as nome,
       SALARIO     as salario
from VENDEDORES
where INATIVO = 0
order by NOME ASC;


-- desafio 2
select ID_VENDEDOR as id,
       NOME        as nome,
       SALARIO     as salario
from VENDEDORES
where SALARIO >
      (select AVG(SALARIO)
       from VENDEDORES)
order by SALARIO DESC;

--desafio 3
select C.ID_CLIENTE                  as id,
       C.RAZAO_SOCIAL                as razao_social,
       ifnull(SUM(P.VALOR_TOTAL), 0) as total
from CLIENTES C
         left join PEDIDO P on C.ID_CLIENTE = P.ID_CLIENTE
group by C.ID_CLIENTE
order by sum(P.VALOR_TOTAL) desc;

--desafio 4
select p.ID_PEDIDO    as id,
       p.VALOR_TOTAL  as valor,
       p.DATA_EMISSAO as data,
       CASE
           WHEN DATA_CANCELAMENTO THEN 'CANCELADO'
           WHEN DATA_FATURAMENTO THEN 'FATURADO'
           WHEN DATA_FATURAMENTO IS NULL AND DATA_CANCELAMENTO IS NULL THEN 'PENDENTE'
           END        as situacao
from PEDIDO p;

--desafio 5
SELECT ip.id_produto                           as id_produto,
       SUM(ip.QUANTIDADE)                      AS quantidade_vendida,
       SUM(ip.QUANTIDADE * ip.PRECO_PRATICADO) AS total_vendido,
       COUNT(DISTINCT ip.id_pedido)            AS pedidos,
       COUNT(DISTINCT p.id_cliente)            AS clientes
FROM itens_pedido ip
         JOIN PEDIDO p ON p.id_pedido = ip.id_pedido
GROUP BY ip.id_produto
ORDER BY quantidade_vendida DESC,
         total_vendido DESC
LIMIT 1;
