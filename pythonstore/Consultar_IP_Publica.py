#comentario:'La consulta de la Ip Publica puede tardar 30 minutos, cuando finalice se le enviará un correo electronico, para que continue con el proceso de identificación del usuario'
import smtplib
from email.message import EmailMessage
##
from Descomprimir import *
from pyspark.sql import SparkSession
spark = SparkSession \
    .builder \
    .master('local[15]') \
    .appName('Notebook') \
    .config('spark.sql.debug.maxToStringFields', 280000) \
    .config('spark.driver.memory', '25g') \
    .getOrCreate()
##
ip_publica = str(input("Ingresa Ip publica a consultar"))
publica = ('"'+ip_publica+'"')

df_cgnat_SourceNatIP_Dest=df_cgnat.filter (f'SourceNatIP == {publica}')
df_cgnat_SourceNatIP_Dest.write.mode("overwrite").csv(f'/bigdata/restore/df_cgnat_SourceNatIP_{ip_publica}_{fecha}')
df_cgnat_SourceNatIP_Dest_file = spark.read.csv(f'/bigdata/restore/df_cgnat_SourceNatIP_{ip_publica}_{fecha}')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c0', 'Fecha')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c1', 'Hora')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c2', 'Min')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c3', 'SourceIP')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c4', 'SourcePort')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c5', 'SourceNatIP')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c6', 'SourceNatPort')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c7', 'DestinationIP')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c8', 'DestinationPort')
df_cgnat_SourceNatIP_Dest_file = df_cgnat_SourceNatIP_Dest_file.withColumnRenamed('_c9', 'Protocol')

#email para encargados
mails=["leandro.riveraact@wom.co"]
msg = EmailMessage()
#Contenido
msg['From']="zabbix@wom.co"
msg['To']=mails
msg['Subject']="La IP Publica ha sido consultada"
cuerpo_del_mail = "Puedes seguir el proceso de identificación de usuario"
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
#server.quit()
print("La ip Publica",ip_publica, "se ha consultado.")