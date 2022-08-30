#comentario:'Espera de 2 a 3 minutos tendras la consulta y continua al siguiente botón'
from Descomprimir import *
from Consultar_IP_Publica import *
from Hora_IP_Publica import *
#Digitar IP Privada a consultar
ip_privada =  str(input("Ingrese ip privada"))

###################################################################################################################
privada = ('"'+ip_privada+'"')

df_merge_cem_ip=df_merge_cem.filter (f'ip_address_assigned == {privada}')
df_merge_cem_ip.write.mode("overwrite").csv(f'/bigdata/restore/df_cem_ip_address_assigned_{ip_privada}_{fecha}')
df_merge_cem_ip_file = spark.read.csv(f'/bigdata/restore/df_cem_ip_address_assigned_{ip_privada}_{fecha}')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c0', 'sequence')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c1', 'start_time')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c2', 'end_time')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c3', 'msisdn')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c4', 'imsi')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c5', 'imei')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c6', 'lac_tac')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c7', 'sac_eci')
df_merge_cem_ip_file = df_merge_cem_ip_file.withColumnRenamed('_c8', 'ip_address_assigned')

print("La ip privada",ip_privada, "se ha consultado.")