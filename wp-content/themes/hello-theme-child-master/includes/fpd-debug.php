<?php
/**
 * Fancy Product Designer Debug-Funktionen
 * 
 * Enthält Funktionen zur Überwachung und Debugging des Fancy Product Designers
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Debug-Ausgabe für Fancy Product Designer
 * Zeigt Konsolenmeldungen an, wenn das WooCommerce Produktbild durch den Fancy Product Designer ersetzt wird
 */
function sg_fpd_debug_output() {
    if (!is_product()) {
        return;
    }
    
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Variablen zur Zustandsverfolgung
        var lastState = {
            designerVisible: false,
            galleryHidden: false,
            activeImageIndex: -1,
            designerActive: false
        };
        var debounceTimer = null;
        var debounceDelay = 300; // Millisekunden
        var checkInterval = null;
        var initialCheckDone = false;
        var fpdWasActive = false; // Verfolgt, ob der FPD aktiv war
        var galleryNavigationInProgress = false; // Verfolgt, ob gerade Galerie-Navigation stattfindet
        
        // Verzögerte initiale Prüfung, um sicherzustellen, dass die Seite vollständig geladen ist
        setTimeout(function() {
            initialCheckDone = true;
            checkFPDReplacement(true);
        }, 1000);
        
        // Funktion zur Prüfung der Ersetzung mit verbesserter Logik
        function checkFPDReplacement(forceLog) {
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            
            debounceTimer = setTimeout(function() {
                // Wenn gerade Galerie-Navigation stattfindet und keine FPD-Aktion ausgelöst wurde, ignorieren
                if (galleryNavigationInProgress && !fpdWasActive) {
                    galleryNavigationInProgress = false;
                    return;
                }
                
                // Aktueller Zustand mit verbesserter Erkennung
                var currentState = {
                    designerVisible: $('.fpd-product-designer-wrapper').length > 0,
                    galleryHidden: $('.woocommerce-product-gallery').length === 0 || 
                                  $('.woocommerce-product-gallery').is(':hidden') || 
                                  $('.woocommerce-product-gallery').css('opacity') === '0',
                    activeImageIndex: getActiveImageIndex(),
                    designerActive: isDesignerActive()
                };
                
                // Nur loggen, wenn sich der Zustand geändert hat oder wenn forceLog true ist
                // UND wenn der Designer tatsächlich aktiv ist oder wenn wir eine Änderung im Konfigurator erkennen
                var configChange = isConfiguratorChange();
                
                if ((forceLog || 
                    currentState.designerVisible !== lastState.designerVisible || 
                    currentState.galleryHidden !== lastState.galleryHidden ||
                    currentState.activeImageIndex !== lastState.activeImageIndex ||
                    currentState.designerActive !== lastState.designerActive) && 
                    (currentState.designerActive || configChange || fpdWasActive) && 
                    !galleryNavigationInProgress) {
                    
                    var imageInfo = getImageInfo(currentState.activeImageIndex);
                    console.log('FPD: WooCommerce Produktbild #' + (currentState.activeImageIndex + 1) + 
                                ' wurde durch Fancy Product Designer ersetzt' + 
                                (imageInfo.title ? ' (' + imageInfo.title + ')' : ''));
                    
                    if (imageInfo.fpdContent) {
                        console.log('FPD: Ersetztes Bild Inhalt: ' + imageInfo.fpdContent.substring(0, 100) + '...');
                    }
                    
                    if (imageInfo.url) {
                        // Wenn es eine Data-URL ist, zeige nur den Anfang
                        if (imageInfo.url.indexOf('data:') === 0) {
                            console.log('FPD: Ersetztes Bild Data-URL: ' + imageInfo.url.substring(0, 50) + '...');
                        } else {
                            console.log('FPD: Ersetztes Bild URL: ' + imageInfo.url);
                        }
                    }
                    
                    if (currentState.galleryHidden) {
                        console.log('FPD: WooCommerce Produktgalerie wurde ausgeblendet oder ersetzt');
                    }
                }
                
                // Zustand aktualisieren
                lastState = currentState;
                galleryNavigationInProgress = false;
            }, debounceDelay);
        }
        
        // Funktion zur Prüfung, ob eine Änderung im Konfigurator stattgefunden hat
        function isConfiguratorChange() {
            // Prüfe, ob der Konfigurator geöffnet ist oder kürzlich geschlossen wurde
            var configuratorOpen = $('.fpd-main-wrapper').length > 0 && $('.fpd-main-wrapper').is(':visible');
            var modalClosed = $('.fpd-modal-close').length > 0 && $('.fpd-modal-close').data('clicked');
            var doneClicked = $('.fpd-done').length > 0 && $('.fpd-done').data('clicked');
            var saveClicked = $('.fpd-save-product').length > 0 && $('.fpd-save-product').data('clicked');
            
            // Prüfe, ob bestimmte FPD-Events kürzlich ausgelöst wurden
            var fpdEventTriggered = window.fpdEventTriggered || false;
            
            // Zurücksetzen der Click-Daten
            $('.fpd-modal-close, .fpd-done, .fpd-save-product').data('clicked', false);
            window.fpdEventTriggered = false;
            
            // Wenn eine dieser Bedingungen erfüllt ist, setze fpdWasActive auf true
            if (configuratorOpen || modalClosed || doneClicked || saveClicked || fpdEventTriggered) {
                fpdWasActive = true;
                return true;
            }
            
            return false;
        }
        
        // Funktion zur Prüfung, ob der Designer tatsächlich aktiv ist und das Produktbild ersetzt
        function isDesignerActive() {
            // Prüfe, ob der FPD-Container vorhanden ist
            var $fpdContainer = $('.fpd-product-designer-wrapper');
            if (!$fpdContainer.length) {
                return false;
            }
            
            // Wenn der FPD aktiv war (durch Benutzerinteraktion oder Events)
            if (fpdWasActive) {
                return true;
            }
            
            // Prüfe, ob der Konfigurator aktiv ist
            var configuratorActive = $('.fpd-actions-wrapper').length > 0 || 
                                    $('.fpd-module').length > 0 ||
                                    $('.fpd-main-wrapper').length > 0;
            
            if (configuratorActive) {
                fpdWasActive = true;
                return true;
            }
            
            return false;
        }
        
        // Funktion zum Ermitteln des aktiven Bildindexes
        function getActiveImageIndex() {
            // Methode 1: Über aktiven Thumbnail
            var $activeThumb = $('.woocommerce-product-gallery__wrapper .flex-control-thumbs li.flex-active-slide, ' + 
                                '.woocommerce-product-gallery__wrapper .flex-control-thumbs li.flex-active');
            if ($activeThumb.length) {
                return $activeThumb.index();
            }
            
            // Methode 2: Über aktives Hauptbild
            var $activeImage = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image.flex-active-slide');
            if ($activeImage.length) {
                return $activeImage.index();
            }
            
            // Methode 3: Über sichtbares Bild
            var $visibleImage = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:visible');
            if ($visibleImage.length) {
                return $visibleImage.index();
            }
            
            // Fallback: Erstes Bild (Index 0)
            return 0;
        }
        
        // Funktion zum Ermitteln von Bildinformationen
        function getImageInfo(index) {
            var info = {
                title: '',
                url: '',
                fpdContent: ''
            };
            
            // Versuche, Alt-Text oder Titel des Bildes zu bekommen (Original WooCommerce Bild)
            var $images = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image');
            if ($images.length > index) {
                var $img = $images.eq(index).find('img');
                if ($img.length) {
                    if ($img.attr('alt')) {
                        info.title = $img.attr('alt');
                    } else if ($img.attr('title')) {
                        info.title = $img.attr('title');
                    } else if ($img.attr('data-caption')) {
                        info.title = $img.attr('data-caption');
                    } else if ($img.attr('data-src')) {
                        // Extrahiere Dateinamen aus Pfad
                        var src = $img.attr('data-src') || $img.attr('src');
                        if (src) {
                            var filename = src.split('/').pop();
                            info.title = filename;
                        }
                    }
                }
            }
            
            // Versuche, das FPD-ersetzte Bild zu finden
            // Methode 1: Suche nach dem Canvas oder Bild des FPD
            var $fpdImage = $('.fpd-product-designer-wrapper canvas, .fpd-product-designer-wrapper img.fpd-image');
            if ($fpdImage.length) {
                // Wenn es ein Canvas ist, hole den Inhalt als Data-URL
                if ($fpdImage.is('canvas')) {
                    try {
                        info.fpdContent = $fpdImage[0].toDataURL('image/png');
                        info.url = info.fpdContent;
                    } catch(e) {
                        console.log('FPD: Fehler beim Abrufen des Canvas-Inhalts', e);
                    }
                } else {
                    // Wenn es ein Bild ist, hole die URL
                    info.url = $fpdImage.attr('src');
                    info.fpdContent = 'Bild: ' + info.url;
                }
            }
            
            // Methode 2: Suche nach dem FPD-Vorschaubild
            if (!info.url) {
                var $fpdPreview = $('.fpd-main-wrapper .fpd-view-stage canvas, .fpd-modal-product-designer .fpd-view-stage canvas');
                if ($fpdPreview.length) {
                    try {
                        info.fpdContent = $fpdPreview[0].toDataURL('image/png');
                        info.url = info.fpdContent;
                    } catch(e) {
                        console.log('FPD: Fehler beim Abrufen des Vorschau-Canvas-Inhalts', e);
                    }
                }
            }
            
            // Methode 3: Versuche, das FPD-Bild über die API zu bekommen
            if (!info.url && typeof fancyProductDesigner !== 'undefined') {
                try {
                    var currentViewIndex = fancyProductDesigner.currentViewIndex;
                    var viewDataURL = fancyProductDesigner.getViewsDataURL()[currentViewIndex];
                    if (viewDataURL) {
                        info.fpdContent = 'FPD API Bild';
                        info.url = viewDataURL;
                    }
                } catch(e) {
                    console.log('FPD: Fehler beim Abrufen des API-Bildes', e);
                }
            }
            
            return info;
        }
        
        // Starte periodische Überprüfung nach Konfigurator-Aktionen
        function startPeriodicCheck() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
            
            // Überprüfe alle 500ms für 5 Sekunden (10 Mal)
            var checkCount = 0;
            checkInterval = setInterval(function() {
                checkFPDReplacement(true); // Erzwinge Ausgabe für periodische Checks
                checkCount++;
                if (checkCount >= 10) {
                    clearInterval(checkInterval);
                    checkInterval = null;
                }
            }, 500);
        }
        
        // MutationObserver für DOM-Änderungen - nur für relevante Elemente
        var observer = new MutationObserver(function(mutations) {
            if (!initialCheckDone) return; // Ignoriere Änderungen vor der initialen Prüfung
            
            var shouldCheck = false;
            
            mutations.forEach(function(mutation) {
                // Nur prüfen, wenn relevante Änderungen erkannt wurden
                if (mutation.target.classList && 
                    (mutation.target.classList.contains('product') || 
                     mutation.target.classList.contains('fpd-product-designer-wrapper') || 
                     mutation.target.classList.contains('woocommerce-product-gallery') ||
                     mutation.target.classList.contains('flex-active-slide') ||
                     mutation.target.classList.contains('flex-active'))) {
                    shouldCheck = true;
                }
                
                // Oder wenn Elemente hinzugefügt/entfernt wurden, die relevant sein könnten
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.classList && 
                            (node.classList.contains('fpd-product-designer-wrapper') || 
                             node.classList.contains('woocommerce-product-gallery') ||
                             node.classList.contains('flex-active-slide') ||
                             node.classList.contains('flex-active'))) {
                            shouldCheck = true;
                            break;
                        }
                    }
                }
            });
            
            if (shouldCheck) {
                checkFPDReplacement(false);
            }
        });
        
        // Beobachte nur den relevanten Bereich der Produktseite
        var productContainer = document.querySelector('.product');
        if (productContainer) {
            observer.observe(productContainer, { 
                childList: true, 
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class', 'display', 'src']
            });
        }
        
        // Zusätzlich auf Klicks auf Buttons achten
        $(document).on('click', '.fpd-modal-close, .fpd-done, .fpd-save-product', function() {
            if (!initialCheckDone) return;
            
            console.log('FPD: Button wurde geklickt');
            
            // Speichere den Klick-Status für spätere Überprüfung
            $(this).data('clicked', true);
            fpdWasActive = true;
            
            // Erzwinge eine sofortige Überprüfung
            checkFPDReplacement(true);
            
            // Starte periodische Überprüfung für verzögerte Änderungen
            startPeriodicCheck();
        });
        
        // Auf Galerie-Navigation achten - markiere als Galerie-Navigation und nicht als FPD-Änderung
        $(document).on('click', '.woocommerce-product-gallery__wrapper .flex-control-thumbs li, .woocommerce-product-gallery__trigger', function() {
            if (!initialCheckDone) return;
            
            // Markiere, dass gerade Galerie-Navigation stattfindet
            galleryNavigationInProgress = true;
            
            // Keine Überprüfung für Thumbnail-Klicks
        });
        
        // Fancy Product Designer spezifische Events
        $(document).on('fpd:productcreate fpd:viewselect fpd:viewshow fpd:productsave fpd:productadd fpd:viewload', function(event) {
            if (!initialCheckDone) return;
            
            console.log('FPD Event ausgelöst: ' + event.type);
            
            // Markiere, dass ein FPD-Event ausgelöst wurde
            window.fpdEventTriggered = true;
            fpdWasActive = true;
            
            // Warte einen Moment, bis die UI-Änderungen abgeschlossen sind
            setTimeout(function() {
                checkFPDReplacement(true); // Erzwinge Ausgabe für FPD-Events
            }, 500);
        });
        
        // Zusätzliche Prüfung nach AJAX-Anfragen (für dynamische Aktualisierungen)
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (!initialCheckDone) return;
            
            // Prüfe, ob die AJAX-Anfrage mit FPD zu tun hat
            if (settings.url && (
                settings.url.indexOf('fancy-product-designer') > -1 || 
                settings.url.indexOf('fpd') > -1 || 
                settings.url.indexOf('wc-ajax') > -1)) {
                console.log('FPD: AJAX-Anfrage abgeschlossen');
                
                // Markiere, dass ein FPD-Event ausgelöst wurde
                window.fpdEventTriggered = true;
                fpdWasActive = true;
                
                setTimeout(function() {
                    checkFPDReplacement(true); // Erzwinge Ausgabe für AJAX-Anfragen
                }, 500);
            }
        });
        
        // Direkte Integration mit FPD API, falls verfügbar
        if (typeof FancyProductDesigner !== 'undefined' || typeof fancyProductDesigner !== 'undefined') {
            console.log('FPD: API erkannt');
            
            // Warte auf vollständige Initialisierung
            setTimeout(function() {
                try {
                    // Versuche, die FPD-Instanz zu finden
                    var fpdInstance = window.fancyProductDesigner || window.fpd;
                    
                    if (fpdInstance) {
                        console.log('FPD: Instanz gefunden, registriere Events');
                        
                        // Registriere Event-Handler für verschiedene FPD-Ereignisse
                        if (typeof fpdInstance.addEventListener === 'function') {
                            fpdInstance.addEventListener('productCreate', function() {
                                if (!initialCheckDone) return;
                                
                                console.log('FPD API: Produkt erstellt');
                                
                                // Markiere, dass ein FPD-Event ausgelöst wurde
                                window.fpdEventTriggered = true;
                                fpdWasActive = true;
                                
                                setTimeout(function() {
                                    checkFPDReplacement(true); // Erzwinge Ausgabe für API-Events
                                }, 500);
                            });
                            
                            fpdInstance.addEventListener('viewSelect', function() {
                                if (!initialCheckDone) return;
                                
                                console.log('FPD API: Ansicht gewechselt');
                                
                                // Markiere, dass ein FPD-Event ausgelöst wurde
                                window.fpdEventTriggered = true;
                                fpdWasActive = true;
                                
                                setTimeout(function() {
                                    checkFPDReplacement(true); // Erzwinge Ausgabe für API-Events
                                }, 500);
                            });
                        }
                    }
                } catch(e) {
                    console.log('FPD: Fehler bei API-Integration', e);
                }
            }, 1000);
        }
    });
    </script>
    <?php
}

// Hook für die Debug-Ausgabe
add_action('wp_footer', 'sg_fpd_debug_output');
