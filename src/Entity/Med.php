<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "med")]
class Med
{
    #[ORM\Id]
    #[ORM\Column(name: "id_medico", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    // 1:1 com Usu (cada médico é um usuário)
    #[ORM\OneToOne(inversedBy: "med", targetEntity: Usu::class)]
    #[ORM\JoinColumn(name: "id_usuario", referencedColumnName: "id_usuario", nullable: false, unique: true, onDelete: "CASCADE")]
    private Usu $usuario;

    #[ORM\Column(name: "nome_medico", type: "string", length: 150)]
    private string $nomeMedico;

    #[ORM\Column(name: "especializacao", type: "string", length: 150)]
    private string $especializacao;

    public function __construct(Usu $usuario, string $nome, string $esp)
    {
        $this->usuario = $usuario;
        $this->nomeMedico = $nome;
        $this->especializacao = $esp;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuario(): Usu { return $this->usuario; }
    public function getNomeMedico(): string { return $this->nomeMedico; }
    public function setNomeMedico(string $v): self { $this->nomeMedico = $v; return $this; }
    public function getEspecializacao(): string { return $this->especializacao; }
    public function setEspecializacao(string $v): self { $this->especializacao = $v; return $this; }
}
