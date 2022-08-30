import sys
import os
import pandas as pd
import glob
import tarfile
from pyspark.sql.types import DoubleType, StructType, StructField, IntegerType, StringType
from pyspark.sql.functions import from_unixtime, col
from pyspark.sql import functions as F
from pyspark.sql.functions import regexp_replace
import shutil
import requests
import smtplib
from email.message import EmailMessage
##
from pyspark.sql import SparkSession
spark = SparkSession \
    .builder \
    .master('local[15]') \
    .appName('Notebook') \
    .config('spark.sql.debug.maxToStringFields', 280000) \
    .config('spark.driver.memory', '25g') \
    .getOrCreate()
##
pathTest =r"/bigdata/restore/logs"
try:
    shutil.rmtree(pathTest)
except OSError as e:
    print(e)
else:
    print("The directory is deleted successfully")
path = '/backup/VM_syslog'

##############################################################################################
#Digitar fecha a consultar Año/Mes/Dia

fecha = str(input("ingrese la fecha: "))
#Descomprime los arvhivos generados por CEM GTP (3g)
gtp = tarfile.open(f'{path}/CEM/Control_EDR_GTP_{fecha}.tar.gz')
gtp.extractall (r'/bigdata/restore/')

#Descomprime los arvhivos generados por CEM GTPv2 (4g)
gtp_v2 = tarfile.open(f'{path}/CEM/Control_EDR_GTPv2_{fecha}.tar.gz')
gtp_v2.extractall (r'/bigdata/restore/')

#Descomprime los arvhivos generados por el CGNAT
cgnat= tarfile.open(f'{path}/CGNAT/logcgnat.{fecha}.tar.gz')
cgnat.extractall (r'/bigdata/restore/')

#Descomprime archivo subscribers IMSI
imsi= tarfile.open(f'{path}/IMSI/CEM_subscribers_{fecha}.tar.gz')
imsi.extractall (r'/bigdata/restore/')

###TRATANDO LA DATA#############
########################################################################################
gtp_files = spark.read.csv(f"/bigdata/restore/logs/cem/SoR_cvs/Control_EDR_GTP_{fecha}*", header=True)
gtp_list = ["sequence","start_time","end_time","msisdn","imsi","imei","lac","sac","ip_address_assigned"]
df_gtp_files = gtp_files.select(gtp_list)
df_gtp_files = df_gtp_files.withColumn("end_time", from_unixtime(col("end_time")/1000000000).cast("timestamp"))
df_gtp_files = df_gtp_files.withColumn("start_time", from_unixtime(col("start_time")/1000000000).cast("timestamp"))
df_gtp_files = df_gtp_files.withColumnRenamed('lac', 'lac_tac')
df_gtp_files = df_gtp_files.withColumnRenamed('sac', 'sac_eci')

gtpv2_files = spark.read.csv(f"/bigdata/restore/logs/cem/SoR_cvs/Control_EDR_GTPv2_{fecha}*", header=True)
col_list_gtpv2 = ["sequence","pdn_start_time","pdn_end_time","msisdn","imsi","imei","tac","eci","ue_ip"]
df_gtpv2_files = gtpv2_files.select(col_list_gtpv2)
df_gtpv2_files = df_gtpv2_files.withColumn("pdn_start_time", from_unixtime(col("pdn_start_time")/1000000000).cast("timestamp"))
df_gtpv2_files = df_gtpv2_files.withColumn("pdn_end_time", from_unixtime(col("pdn_end_time")/1000000000).cast("timestamp"))
df_gtp_files = df_gtp_files.withColumn('start_time', regexp_replace('start_time', '-', '_'))
df_gtp_files = df_gtp_files.withColumn('end_time', regexp_replace('end_time', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('tac', regexp_replace('tac', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('eci', regexp_replace('eci', '-', '_'))


df_gtpv2_files = df_gtpv2_files.withColumnRenamed('pdn_start_time', 'start_time')
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('pdn_end_time', 'end_time')
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('ue_ip', 'ip_address_assigned')
df_gtpv2_files = df_gtpv2_files.withColumn('start_time', regexp_replace('start_time', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('end_time', regexp_replace('end_time', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('pdn_start_time', 'start_time')
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('pdn_end_time', 'end_time')
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('ue_ip', 'ip_address_assigned')
df_gtpv2_files = df_gtpv2_files.withColumn('start_time', regexp_replace('start_time', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('end_time', regexp_replace('end_time', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('tac', regexp_replace('tac', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumn('eci', regexp_replace('eci', '-', '_'))
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('tac', 'lac_tac')
df_gtpv2_files = df_gtpv2_files.withColumnRenamed('eci', 'sac_eci')

df_merge_cem = df_gtp_files.union(df_gtpv2_files)
cgnat= spark.read.csv(f"/bigdata/restore/logs/rsyslog/logcgnat.{fecha}*.log", sep= ';')
cgnat_cols = F.split(cgnat['_c0'], ' ')
df_cgnat = cgnat.withColumn('Fecha', cgnat_cols.getItem(1)) \
.withColumn('Hora', cgnat_cols.getItem(2)) \
.withColumn('Min', cgnat_cols.getItem(3)) \
.withColumn('_c1', F.split('_c1', 'Source_IP')[1].cast(StringType()))\
.withColumn('_c2', F.split('_c2', 'SourcePort=')[1].cast(StringType()))\
.withColumn('_c3', F.split('_c3', 'SourceNatIP=')[1].cast(StringType()))\
.withColumn('_c4', F.split('_c4', 'SourceNatPort=')[1].cast(StringType()))\
.withColumn('_c5', F.split('_c5', 'DestinationIP=')[1].cast(StringType()))\
.withColumn('_c6', F.split('_c6', 'DestinationPort=')[1].cast(StringType()))\
.withColumn('_c7', F.split('_c7', 'Protocol=')[1].cast(StringType()))


cgnat_list = ["Fecha", "Hora", "Min", "_c1", "_c2", "_c3", "_c4", "_c5", "_c6", "_c7"]
df_cgnat = df_cgnat.select(cgnat_list)

df_cgnat = df_cgnat.withColumnRenamed('_c1', 'SourceIP')
df_cgnat = df_cgnat.withColumnRenamed('_c2', 'SourcePort')
df_cgnat = df_cgnat.withColumnRenamed('_c3', 'SourceNatIP')
df_cgnat = df_cgnat.withColumnRenamed('_c4', 'SourceNatPort')
df_cgnat = df_cgnat.withColumnRenamed('_c5', 'DestinationIP')
df_cgnat = df_cgnat.withColumnRenamed('_c6', 'DestinationPort')
df_cgnat = df_cgnat.withColumnRenamed('_c7', 'Protocol')

#################################################################################################
### EMail para encargados
mails=["leandro.riveraact@wom.co"]
msg = EmailMessage()
#Contenido
msg['From']="zabbix@wom.co"
msg['To']=mails
msg['Subject']="Descompresion de archivos zeppelin"
cuerpo_del_mail = "Descompresion de archivos zeppelin ha terminado correctamente"
msg.set_content(cuerpo_del_mail)
#INICIAR SERVER
server = smtplib.SMTP('smtp.office365.com')
server.starttls()
#USUARIO Y CONTRASEÑA
usuario = 'zabbix@wom.co'
password = 'Wux37874'
server.login(usuario, password)
#ENVIAR CORREO
server.send_message(msg)
server.quit()
print("Se ha realizado la descompresión de la fecha", fecha)