<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "relatorio")]
class Relatorio
{
    #[ORM\Id]
    #[ORM\Column(name: "cod_relatorio", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    // N:1 (muitos relatórios por usuário)
    #[ORM\ManyToOne(inversedBy: "relatorios", targetEntity: Usu::class)]
    #[ORM\JoinColumn(name: "id_usuario", referencedColumnName: "id_usuario", nullable: false, onDelete: "CASCADE")]
    private Usu $usuario;

    #[ORM\Column(name: "titulo_relatorio", type: "string", length: 200)]
    private string $titulo;

    public function __construct(Usu $usuario, string $titulo)
    {
        $this->usuario = $usuario;
        $this->titulo = $titulo;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuario(): Usu { return $this->usuario; }
    public function getTitulo(): string { return $this->titulo; }
    public function setTitulo(string $v): self { $this->titulo = $v; return $this; }
}
