<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "usu")]
class Usu
{
    #[ORM\Id]
    #[ORM\Column(name: "id_usuario", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(name: "login", type: "string", length: 150, unique: true)]
    private string $login;

    #[ORM\Column(name: "senha", type: "string", length: 255)]
    private string $senha;

    #[ORM\Column(name: "tipo_usuario", type: "integer")]
    private int $tipoUsuario;

    // Relacionamentos 1:1 opcionais com Med e Adm
    #[ORM\OneToOne(mappedBy: "usuario", targetEntity: Med::class, cascade: ["persist", "remove"])]
    private ?Med $med = null;

    #[ORM\OneToOne(mappedBy: "usuario", targetEntity: Adm::class, cascade: ["persist", "remove"])]
    private ?Adm $adm = null;

    // Relatórios (1 Usu -> N Relatorios)
    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Relatorio::class, cascade: ["persist", "remove"])]
    private iterable $relatorios;

    public function __construct(string $login, string $senhaHash, int $tipoUsuario)
    {
        $this->login = $login;
        $this->senha = $senhaHash;
        $this->tipoUsuario = $tipoUsuario;
        $this->relatorios = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getLogin(): string { return $this->login; }
    public function setLogin(string $v): self { $this->login = $v; return $this; }

    public function getSenha(): string { return $this->senha; }
    public function setSenha(string $hash): self { $this->senha = $hash; return $this; }

    public function getTipoUsuario(): int { return $this->tipoUsuario; }
    public function setTipoUsuario(int $v): self { $this->tipoUsuario = $v; return $this; }

    public function getMed(): ?Med { return $this->med; }
    public function setMed(?Med $m): self { $this->med = $m; return $this; }

    public function getAdm(): ?Adm { return $this->adm; }
    public function setAdm(?Adm $a): self { $this->adm = $a; return $this; }
}
