# Importa a função principal para criar a conexão com o banco de dados.
from sqlalchemy import create_engine
# Importa Session (para gerenciar a conversa com o DB) e declarative_base (para criar modelos).
from sqlalchemy.orm import Session, declarative_base
# Importa automap_base (ferramenta de Database First)
from sqlalchemy.ext.automap import automap_base


DATABASE_URL = "mysql+pymysql://root:PUC%401234@localhost:3306/clinica_simples"


# motor de conexão (comunicar com o DB)
engine = create_engine(DATABASE_URL)

Base = declarative_base()
Base = automap_base(cls=Base)
Base.prepare(autoload_with=engine)


# As classes geradas são acessadas pelo nome da tabela
Paciente = Base.classes.pacientes
Medico = Base.classes.medicos
Consulta = Base.classes.consultas
Especialidade = Base.classes.especialidades

print("=" * 40)
print("SUCESSO! Os modelos Python foram gerados automaticamente.")
print("Classes disponíveis: Paciente, Medico, Consulta, Especialidade")
print("=" * 40)


# Demonstração do uso dos modelos gerados

with Session(engine) as session: # Cria uma sessão para operações no DB
    print("\n--- Buscando Dados do Banco ---")

    consulta_obj = session.query(Consulta).first() #Cria uma consulta para a Classe/Tabela 'Consultas'

    if consulta_obj:
        print(f"ID da Consulta: {consulta_obj.id}")

        paciente = consulta_obj.pacientes
        medico = consulta_obj.medicos
        print(f"Paciente: {paciente.nome}")
        print(f"Médico: {medico.nome}")
        print(f"Especialidade do Médico: {medico.especialidades.nome}")

    else:
        print("Nenhuma consulta encontrada. Verifique se inseriu os dados de teste.")