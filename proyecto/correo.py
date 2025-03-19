import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import sys

# Capturar los correos pasados desde PHP
correos = sys.argv[1].split(",")  # Los correos están separados por coma

# Configuración de acceso
smtp_server = "smtp.gmail.com"
smtp_port = 587
email_user = "webtrabajos0@gmail.com"
email_pass = "oits qeyf hygn zhoz"

# Crear el objeto MIME para el correo
msg = MIMEMultipart()
msg["From"] = email_user
msg["Subject"] = "Se recivio el correo"

# Cuerpo del correo
body = "Se buscara la mejor respuesta para ti."
msg.attach(MIMEText(body, "plain"))

# Conexión al servidor SMTP
server = smtplib.SMTP(smtp_server, smtp_port)
server.starttls()  # Iniciar conexión segura

# Iniciar sesión en el servidor
server.login(email_user, email_pass)

# Enviar el correo a todos los correos
for correo in correos:
    msg["To"] = correo
    server.sendmail(email_user, correo, msg.as_string())

# Cerrar la conexión
server.quit()

print("Correos enviados a:", ", ".join(correos))  # Imprime la lista de correos
