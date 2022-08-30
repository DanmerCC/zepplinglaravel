import pandas as pd
from Descomprimir import *
from Consultar_IP_Publica import *
#IP Minuot/hora
hourr = str(input("Ingresa la hora a consultar Ip Publica"))
##minute = str(input("Ingresa el minuto IP publica a consultar"))

df_cgnat_SourceNatIP_hora=df_cgnat_SourceNatIP_Dest_file.where(F.col("Hora").like(f'{hourr}%'))
##df_cgnat_SourceNatIP_hora_min=df_cgnat_SourceNatIP_hora.where(F.col("Min").like(f'{minute}%'))

df_cgnat_SourceNatIP_hora.show(df_cgnat_SourceNatIP_hora.count())
##df_cgnat_SourceNatIP_hora_min.show(df_cgnat_SourceNatIP_hora_min.count())
#df_cgnat_SourceNatIP_min.show()