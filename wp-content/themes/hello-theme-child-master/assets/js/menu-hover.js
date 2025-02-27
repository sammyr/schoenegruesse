// Menü-Hover-System
document.addEventListener('DOMContentLoaded', function() {
    // Speichere alle Menübilder in einem Objekt
    const menuImages = {
        'menu-item-627': {
            post_id: '6062',
            title: 'Geburtstagsanzeige Jungen',
            bilddatei_url: '/wp-content/uploads/2019/08/Schoene-Post-Menu-Geburtsanzeige-Jungen-1.jpg'
        },
        'menu-item-2525': {
            post_id: '6060',
            title: 'Geburtstagsanzeige Mädchen',
            bilddatei_url: '/wp-content/uploads/2019/08/Schoene-Post-Menu-Geburtsanzeige-Maedchen-1.jpg'
        },
        // ... weitere Menüeinträge hier
    };

    // Erstelle Container für Vorschaubilder
    const previewContainer = document.createElement('div');
    previewContainer.className = 'menu-preview-container';
    document.body.appendChild(previewContainer);

    // Hover-Events für Menüeinträge
    Object.keys(menuImages).forEach(menuId => {
        const menuItem = document.getElementById(menuId);
        if (menuItem) {
            menuItem.addEventListener('mouseenter', function() {
                const imageData = menuImages[menuId];
                if (imageData) {
                    showPreview(imageData, this);
                }
            });

            menuItem.addEventListener('mouseleave', function() {
                hidePreview();
            });
        }
    });

    // Zeige Vorschau
    function showPreview(imageData, element) {
        previewContainer.innerHTML = `
            <div class="menu-preview-image">
                <img src="${imageData.bilddatei_url}" alt="${imageData.title}">
                <div class="menu-preview-title">${imageData.title}</div>
            </div>
        `;
        
        // Positioniere Vorschau
        const rect = element.getBoundingClientRect();
        previewContainer.style.display = 'block';
        previewContainer.style.left = `${rect.right + 10}px`;
        previewContainer.style.top = `${rect.top}px`;
    }

    // Verstecke Vorschau
    function hidePreview() {
        previewContainer.style.display = 'none';
    }
});
