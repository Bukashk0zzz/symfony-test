<?php declare(strict_types = 1);

namespace App\Entity\User;

use Bukashk0zzz\FilterBundle\Annotation\FilterAnnotation as Filter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unq_username", columns={"username"})
 * })
 * @UniqueEntity(
 *     fields={"username"},
 *     message="usernameTaken",
 *     groups={"Default"}
 * )
 */
class User implements UserInterface, \Serializable
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     *
     * @Filter("StripTags")
     * @Filter("StringTrim")
     * @Filter("StripNewlines")
     *
     * @Assert\NotBlank(groups={"Edit"})
     * @Assert\Length(
     *     min=2,
     *     max=140,
     *     groups={"Edit"}
     * )
     *
     * @var string|null
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Filter("StripTags")
     * @Filter("StringTrim")
     * @Filter("StripNewlines")
     *
     * @Assert\Email(groups={"Edit", "Login", "Restore"})
     * @Assert\NotBlank(groups={"Edit", "Login", "Restore"})
     *
     * @JMS\Groups({"Default"})
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     *
     * @Assert\NotBlank(groups={"Edit"})
     * @AssertPhoneNumber(groups={"Edit"})
     *
     * @JMS\Type("libphonenumber\PhoneNumber")
     *
     * @SWG\Property(type="string")
     *
     * @var PhoneNumber|null
     */
    protected $phone;

    /**
     * @Assert\NotBlank(groups={"Login", "ChangePassword", "CreatePassword"})
     * @Assert\Length(
     *     min=2,
     *     max=64,
     *     groups={"Login", "ChangePassword", "CreatePassword"}
     * )
     * @JMS\Exclude()
     *
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @JMS\Exclude()
     *
     * @var string|null
     */
    protected $password;

    /**
     * @ORM\Column(type="array", nullable=false)
     *
     * @var array<string>
     */
    protected $roles;

    /**
     * @ORM\Column(name="`active`", type="boolean", nullable=false)
     *
     * @var bool
     */
    protected $active = true;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false, length=64)
     *
     * @JMS\Groups({"UserPrivate"})
     *
     * @var string
     */
    protected $apiKey;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $lastActive;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->updateApiKey();
        $this->plainPassword = $this->getApiKey();
        $this->setRoles(['ROLE_USER']);
        $this->lastActive = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) ($this->getName() ?: $this->getUsername());
    }

    /**
     * @return void
     */
    public function updateApiKey(): void
    {
        $this->apiKey = \sha1(\uniqid('ApiKey', true).\time());
    }

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @return string
     *
     * @see \Serializable::serialize()
     */
    public function serialize(): ?string
    {
        return \serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->active,
        ]);
    }

    /**
     * @param string|null $serialized
     *
     * @return void
     */
    public function unserialize($serialized): void
    {
        /** @noinspection UnserializeExploitsInspection */
        [
            $this->id,
            $this->username,
            $this->password,
            $this->active,
        ] = \unserialize($serialized);
    }

    // @codingStandardsIgnoreEnd

    /**
     * Erase Credentials
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string|null $username
     *
     * @return User
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Set password
     *
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array<string> $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set active
     *
     * @param bool|null $active
     *
     * @return User
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * Set apiKey
     *
     * @param string|null $apiKey
     *
     * @return User
     */
    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @param string|null $name
     *
     * @return User
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    /**
     * @param PhoneNumber|null $phone
     *
     * @return User
     */
    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @param \DateTime|null $lastActive
     *
     * @return User
     */
    public function setLastActive(?\DateTime $lastActive): self
    {
        $this->lastActive = $lastActive;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActive(): ?\DateTime
    {
        return $this->lastActive;
    }
}
