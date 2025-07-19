<?php
class PetCard {
    private $pet;
    
    public function __construct($petData) {
        $this->pet = $petData;
    }
    
    public function render() {
        $name = htmlspecialchars($this->pet['name']);
        $type = htmlspecialchars($this->pet['type']);
        $breed = htmlspecialchars($this->pet['breed']);
        $age = htmlspecialchars($this->pet['age']);
        $gender = htmlspecialchars($this->pet['gender']);
        $location = htmlspecialchars($this->pet['location']);
        $description = htmlspecialchars($this->pet['description']);
        $image = htmlspecialchars($this->pet['image'] ?? '');
        $petId = htmlspecialchars($this->pet['id']);
        
        echo "
        <div class='pet-card' data-pet-id='{$petId}'>
            <div class='pet-image-container'>
                <img src='{$image}' alt='{$name}' class='pet-image' />
                <button class='favorite-btn' onclick='toggleFavorite({$petId})'>
                    <svg class='heart-icon' viewBox='0 0 24 24' fill='none'>
                        <path d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>
                </button>
            </div>
            
            <div class='pet-info'>
                <div class='pet-header'>
                    <h3 class='pet-name'>{$name}</h3>
                    <span class='pet-type'>{$type}</span>
                </div>
                
                <div class='pet-details'>
                    <span class='detail-item'>{$breed}</span>
                    <span class='detail-separator'>•</span>
                    <span class='detail-item'>{$age} years</span>
                    <span class='detail-separator'>•</span>
                    <span class='detail-item'>{$gender}</span>
                </div>
                
                <div class='pet-location'>
                    <svg class='location-icon' viewBox='0 0 24 24' fill='none'>
                        <path d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z' stroke='currentColor' stroke-width='2'/>
                        <circle cx='12' cy='10' r='3' stroke='currentColor' stroke-width='2'/>
                    </svg>
                    <span>{$location}</span>
                </div>
                
                <p class='pet-description'>{$description}</p>
            </div>
        </div>";
    }
}

// Usage:
// render in petSearch page
?>