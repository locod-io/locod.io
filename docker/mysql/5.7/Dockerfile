# This dockerfile is used to run the tests by the gitlab runner.
FROM mysql:5.7.21

# left out ONLY_FULL_GROUP_BY below to enable hacky group-by functionality in SimpleReadModelDBALRepository
# this is no problem since we have this on the real server as well.
RUN echo sql-mode=STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION >> /etc/mysql/mysql.conf.d/mysqld.cnf
