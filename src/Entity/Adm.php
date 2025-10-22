<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "adm")]
class Adm
{
    #[ORM\Id]
    #[ORM\Column(name: "id_adm", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    // 1:1 com Usu (cada admin é um usuário)
    #[ORM\OneToOne(inversedBy: "adm", targetEntity: Usu::class)]
    #[ORM\JoinColumn(name: "id_usuario", referencedColumnName: "id_usuario", nullable: false, unique: true, onDelete: "CASCADE")]
    private Usu $usuario;

    #[ORM\Column(name: "nome_adm", type: "string", length: 150)]
    private string $nomeAdm;

    public function __construct(Usu $usuario, string $nomeAdm)
    {
        $this->usuario = $usuario;
        $this->nomeAdm = $nomeAdm;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuario(): Usu { return $this->usuario; }
    public function getNomeAdm(): string { return $this->nomeAdm; }
    public function setNomeAdm(string $v): self { $this->nomeAdm = $v; return $this; }
}
