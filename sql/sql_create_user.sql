CREATE USER 'snippet'@'%' IDENTIFIED BY 'snippetUser@0605';
GRANT SELECT ON snippet.* TO 'snippet'@'%';
GRANT INSERT ON snippet.* TO 'snippet'@'%';
GRANT UPDATE ON snippet.* TO 'snippet'@'%';
GRANT DELETE ON snippet.* TO 'snippet'@'%';