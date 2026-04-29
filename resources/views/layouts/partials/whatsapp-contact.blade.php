{{-- WhatsApp Contact Partial --}}
<div class="whatsapp-contact-container" id="whatsapp-contact-container">
    <!-- Logo WhatsApp (toujours visible) -->
    <div class="whatsapp-logo" id="whatsapp-logo" onclick="toggleWhatsAppForm()">
        <i class="bi bi-whatsapp"></i>
        <span class="whatsapp-pulse"></span>
    </div>

    <!-- Formulaire WhatsApp (caché par défaut) -->
    <div class="whatsapp-contact-card" id="whatsapp-contact-card">
        <div class="whatsapp-header">
            <i class="bi bi-whatsapp"></i>
            <h5>Contactez-nous via WhatsApp</h5>
            <button class="whatsapp-close" onclick="toggleWhatsAppForm()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="whatsapp-body">
            <p>Besoin d'aide ? Contactez-nous directement sur WhatsApp pour une réponse rapide.</p>
            <div class="whatsapp-number">
                <i class="bi bi-telephone-fill"></i>
                <span>+228 929 808 42</span>
            </div>
            <a href="https://wa.me/22892980842" 
               target="_blank" 
               class="btn btn-success whatsapp-btn">
                <i class="bi bi-whatsapp"></i>
                Envoyer un message
            </a>
        </div>
    </div>
</div>

<script>
function toggleWhatsAppForm() {
    const container = document.getElementById('whatsapp-contact-container');
    const card = document.getElementById('whatsapp-contact-card');
    const logo = document.getElementById('whatsapp-logo');
    
    if (card.classList.contains('expanded')) {
        // Réduire le formulaire
        card.classList.remove('expanded');
        logo.classList.remove('hidden');
        container.classList.remove('expanded');
    } else {
        // Étendre le formulaire
        card.classList.add('expanded');
        logo.classList.add('hidden');
        container.classList.add('expanded');
    }
}

// Fermer le formulaire en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const container = document.getElementById('whatsapp-contact-container');
    const card = document.getElementById('whatsapp-contact-card');
    const logo = document.getElementById('whatsapp-logo');
    
    if (!container.contains(event.target)) {
        card.classList.remove('expanded');
        logo.classList.remove('hidden');
        container.classList.remove('expanded');
    }
});
</script>

<style>
.whatsapp-contact-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease;
}

/* Logo WhatsApp (toujours visible) */
.whatsapp-logo {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #25D366, #128C7E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.whatsapp-logo:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
}

.whatsapp-logo.hidden {
    opacity: 0;
    transform: scale(0);
    pointer-events: none;
}

.whatsapp-logo i {
    color: white;
    font-size: 28px;
    z-index: 2;
    position: relative;
}

.whatsapp-pulse {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #25D366;
    border-radius: 50%;
    animation: pulse 2s infinite;
    opacity: 0.3;
}

/* Formulaire WhatsApp */
.whatsapp-contact-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 320px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: 1px solid #e0e0e0;
    position: absolute;
    bottom: 80px;
    right: 0;
    opacity: 0;
    transform: scale(0.8) translateY(20px);
    pointer-events: none;
}

.whatsapp-contact-card.expanded {
    opacity: 1;
    transform: scale(1) translateY(0);
    pointer-events: auto;
}

/* Le container ne se déplace plus - le formulaire reste dans le coin */
.whatsapp-contact-container.expanded {
    /* transform: translateX(-340px); */ /* Retiré */
}

.whatsapp-header {
    background: linear-gradient(135deg, #25D366, #128C7E);
    color: white;
    padding: 15px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.whatsapp-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    flex: 1;
}

.whatsapp-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.whatsapp-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.whatsapp-body {
    padding: 20px;
    text-align: center;
}

.whatsapp-body p {
    margin: 0 0 15px 0;
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

.whatsapp-number {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 15px;
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.whatsapp-number i {
    color: #25D366;
    font-size: 18px;
}

.whatsapp-btn {
    width: 100%;
    padding: 12px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.whatsapp-btn:hover {
    background: #128C7E !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
}

.whatsapp-btn i {
    font-size: 16px;
}

/* Animations */
@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.1;
    }
    100% {
        transform: scale(1);
        opacity: 0.3;
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes slideOutDown {
    from {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    to {
        opacity: 0;
        transform: scale(0.8) translateY(20px);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .whatsapp-contact-container {
        bottom: 15px;
        right: 15px;
    }
    
    .whatsapp-logo {
        width: 50px;
        height: 50px;
    }
    
    .whatsapp-logo i {
        font-size: 24px;
    }
    
    .whatsapp-contact-card {
        width: 280px;
        bottom: 70px; /* Ajusté pour mobile */
        right: 0; /* Reste aligné à droite */
    }
    
    /* Le container ne se déplace plus sur mobile non plus */
    .whatsapp-contact-container.expanded {
        /* transform: translateX(-300px); */ /* Retiré */
    }
    
    .whatsapp-header h5 {
        font-size: 14px;
    }
    
    .whatsapp-body p {
        font-size: 13px;
    }
    
    .whatsapp-number {
        font-size: 15px;
    }
}

/* Animation d'entrée au chargement */
.whatsapp-contact-card {
    animation: slideInUp 0.5s ease-out;
}

/* Éviter les conflits avec d'autres éléments */
.whatsapp-contact-container * {
    box-sizing: border-box;
}
</style>
