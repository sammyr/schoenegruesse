/**
 * FPD Galerie-Integration
 * Ersetzt alle Bilder in der WooCommerce-Galerie mit FPD-Bildern
 */
jQuery(document).ready(function($) {
    // Kurze Verzögerung, um sicherzustellen, dass alles geladen ist
    setTimeout(function() {
        if ($('.fpd-container').length) {
            console.log('FPD: Initialisiere Galerie-Integration');
            initFPDGalleryIntegration();
        }
    }, 1000);
    
    // Zusätzlicher Check für später geladene FPD-Container
    var checkInterval = setInterval(function() {
        if ($('.fpd-container').length && !window.fpdGalleryInitialized) {
            console.log('FPD: FPD-Container nachträglich gefunden, initialisiere Galerie-Integration');
            initFPDGalleryIntegration();
            window.fpdGalleryInitialized = true;
        }
    }, 2000);
    
    // Nach 20 Sekunden Interval stoppen
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 20000);
    
    // FPD Galerie-Integration - Ersetzt alle Bilder in der WooCommerce-Galerie mit FPD-Bildern
    function initFPDGalleryIntegration() {
        
        // Nur ausführen, wenn der FPD aktiv ist
        if (typeof fancyProductDesigner === 'undefined') {
            // Warten auf FPD-Initialisierung
            var fpdCheckInterval = setInterval(function() {
                if (typeof fancyProductDesigner !== 'undefined') {
                    console.log('FPD: Fancy Product Designer gefunden, starte Galerie-Integration');
                    clearInterval(fpdCheckInterval);
                    initFPDEvents();
                }
            }, 500);
            
            // Nach 10 Sekunden aufgeben
            setTimeout(function() {
                clearInterval(fpdCheckInterval);
            }, 10000);
            
            return;
        }
        
        initFPDEvents();
        
        function initFPDEvents() {
            // Funktion zum Ersetzen der Bilder in der Galerie
            function replaceGalleryImages() {
                // Prüfen, ob der FPD aktiv ist und Bilder hat
                if (typeof fancyProductDesigner === 'undefined') return;
                
                try {
                    // Prüfen, ob der FPD eine aktive Ansicht hat
                    if (!fancyProductDesigner.currentViewInstance) {
                        console.log('FPD: Keine aktive Ansicht vorhanden, warte auf Initialisierung...');
                        return;
                    }
                    
                    // Alle Ansichten aus dem FPD als Base64-Bilder holen
                    var fpdViews = [];
                    try {
                        fpdViews = fancyProductDesigner.getViewsDataURL();
                    } catch (e) {
                        console.log('FPD: Fehler beim Abrufen der Ansichten, versuche alternative Methode');
                        // Alternative Methode: Einzelne Ansichten manuell abrufen
                        if (fancyProductDesigner.viewInstances && fancyProductDesigner.viewInstances.length) {
                            for (var i = 0; i < fancyProductDesigner.viewInstances.length; i++) {
                                if (fancyProductDesigner.viewInstances[i]) {
                                    try {
                                        var viewDataURL = fancyProductDesigner.viewInstances[i].toDataURL();
                                        fpdViews.push(viewDataURL);
                                    } catch (viewErr) {
                                        console.error('FPD: Fehler beim Abrufen der Ansicht ' + i, viewErr);
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!fpdViews || !fpdViews.length) {
                        console.log('FPD: Keine Ansichten gefunden, überspringe Galerie-Update');
                        return;
                    }
                    
                    console.log('FPD: ' + fpdViews.length + ' Bilder aus dem Produktkonfigurator gefunden');
                    
                    // Alle Bilder in der WooCommerce-Galerie finden
                    var $galleryImages = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image');
                    
                    // Wenn wir mehr FPD-Ansichten als Galerie-Bilder haben, nur die vorhandenen ersetzen
                    var imagesToReplace = Math.min(fpdViews.length, $galleryImages.length);
                    
                    // Jedes Bild in der Galerie durch ein FPD-Bild ersetzen
                    for (var i = 0; i < imagesToReplace; i++) {
                        var $currentImage = $galleryImages.eq(i);
                        var $img = $currentImage.find('img');
                        var $link = $currentImage.find('a');
                        
                        // Original-Attribute speichern
                        if (!$img.attr('data-original-src') && $img.attr('src')) {
                            $img.attr('data-original-src', $img.attr('src'));
                        }
                        
                        if (!$link.attr('data-original-href') && $link.attr('href')) {
                            $link.attr('data-original-href', $link.attr('href'));
                        }
                        
                        // Bild und Link mit FPD-Bild ersetzen
                        $img.attr('src', fpdViews[i]);
                        $img.attr('data-src', fpdViews[i]);
                        $img.attr('data-large_image', fpdViews[i]);
                        $link.attr('href', fpdViews[i]);
                        
                        console.log('FPD: Galeriebild #' + (i+1) + ' durch FPD-Bild ersetzt');
                    }
                    
                    // Wenn wir die Galerie-Initialisierung beeinflussen müssen
                    if (typeof $galleryImages.data('flexslider') !== 'undefined') {
                        $galleryImages.data('flexslider').resize();
                    }
                    
                    // Wenn Photoswipe oder andere Lightbox verwendet wird, Event auslösen
                    $('body').trigger('wc-product-gallery-after-init');
                } catch (e) {
                    console.error('FPD: Fehler beim Ersetzen der Galeriebilder', e);
                }
            }
            
            // Funktion zum Wiederherstellen der Original-Bilder
            function restoreGalleryImages() {
                var $galleryImages = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image');
                
                $galleryImages.each(function(index) {
                    var $img = $(this).find('img');
                    var $link = $(this).find('a');
                    
                    // Original-Bilder wiederherstellen, wenn vorhanden
                    if ($img.attr('data-original-src')) {
                        $img.attr('src', $img.attr('data-original-src'));
                        $img.attr('data-src', $img.attr('data-original-src'));
                        $img.attr('data-large_image', $img.attr('data-original-src'));
                    }
                    
                    if ($link.attr('data-original-href')) {
                        $link.attr('href', $link.attr('data-original-href'));
                    }
                });
                
                console.log('FPD: Originalbilder in der Galerie wiederhergestellt');
            }
            
            // Events für den FPD registrieren
            
            // Wenn der FPD fertig geladen ist
            $(document).on('productCreate', function() {
                console.log('FPD: Produktkonfigurator geladen, initialisiere Galerie-Integration');
                // Längere Verzögerung, um sicherzustellen, dass alle Ansichten geladen sind
                setTimeout(replaceGalleryImages, 1000);
            });
            
            // Wenn sich die Ansicht im FPD ändert
            $(document).on('viewSelect', function(event, index) {
                console.log('FPD: Ansicht im Produktkonfigurator geändert zu #' + (index+1));
                // Zur entsprechenden Ansicht in der Galerie wechseln
                if ($('.flex-control-nav.flex-control-thumbs li').length > index) {
                    $('.flex-control-nav.flex-control-thumbs li').eq(index).find('img').trigger('click');
                }
            });
            
            // Wenn der Benutzer etwas im FPD ändert
            $(document).on('elementModify', function() {
                // Kurze Verzögerung, um sicherzustellen, dass die Änderungen angewendet wurden
                setTimeout(function() {
                    // Bilder aktualisieren, wenn sich etwas im Designer ändert
                    replaceGalleryImages();
                }, 300);
            });
            
            // Wenn der Benutzer in der Galerie navigiert
            $('.flex-control-nav.flex-control-thumbs li img').on('click', function() {
                var index = $(this).parent().index();
                console.log('FPD: Galerie-Navigation zu Bild #' + (index+1));
                
                // Zur entsprechenden Ansicht im FPD wechseln
                if (typeof fancyProductDesigner !== 'undefined' && fancyProductDesigner.viewInstances && fancyProductDesigner.viewInstances.length > index) {
                    fancyProductDesigner.selectView(index);
                }
            });
            
            // Dynamisch hinzugefügte Galerie-Elemente überwachen
            $(document).on('click', '.flex-control-nav.flex-control-thumbs li img', function() {
                var index = $(this).parent().index();
                console.log('FPD: Galerie-Navigation zu Bild #' + (index+1) + ' (dynamisch)');
                
                // Zur entsprechenden Ansicht im FPD wechseln
                if (typeof fancyProductDesigner !== 'undefined' && fancyProductDesigner.viewInstances && fancyProductDesigner.viewInstances.length > index) {
                    fancyProductDesigner.selectView(index);
                }
            });
            
            // Wenn der FPD geschlossen wird
            $(document).on('modalDesignerClose', function() {
                console.log('FPD: Produktkonfigurator geschlossen, stelle Originalbilder wieder her');
                restoreGalleryImages();
            });
        }
    }
});
