from Descomprimir import *
from Consultar_IP_Publica import *
from Hora_IP_Publica import *
from IP_Privada import *

hour = str(input("Ingrese hora de la ip Privada "))
houre = str(input("Ingrese la hora 2 de la ip Privada "))

df_merge_cem_hora=df_merge_cem_ip_file.filter(F.col("start_time").between(f'{fecha} {hour}:%',f'{fecha} {houre}:%'))

#df_merge_cem_hora.show(df_merge_cem_hora.count())
df_merge_cem_hora.orderBy(["start_time"], descending=[0, 0]).show(1000)