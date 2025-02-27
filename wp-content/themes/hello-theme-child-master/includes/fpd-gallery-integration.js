/**
 * FPD Galerie-Integration
 * Ersetzt alle Bilder in der WooCommerce-Galerie mit FPD-Bildern
 * Version: 1.0.7
 */
jQuery(document).ready(function($) {
    // Globale Variablen
    var fpdGalleryInitialized = false;
    var originalGalleryImages = [];
    var debugMode = true;
    
    // Debug-Funktion
    function debugLog(message, data) {
        if (debugMode) {
            if (data) {
                console.log('FPD DEBUG: ' + message, data);
            } else {
                console.log('FPD DEBUG: ' + message);
            }
        }
    }
    
    // Warte auf jQuery und FPD
    var waitForFPD = setInterval(function() {
        if (typeof $ !== 'undefined' && $('.fpd-container').length) {
            clearInterval(waitForFPD);
            debugLog('FPD-Container gefunden, starte Initialisierung');
            
            // Warte auf die vollständige Initialisierung des FPD
            var waitForFPDInstance = setInterval(function() {
                if (typeof window.fancyProductDesigner !== 'undefined') {
                    clearInterval(waitForFPDInstance);
                    debugLog('FPD-Instance gefunden, starte Galerie-Integration');
                    initFPDGalleryIntegration();
                }
            }, 500);
            
            // Nach 20 Sekunden aufgeben
            setTimeout(function() {
                clearInterval(waitForFPDInstance);
                debugLog('Timeout beim Warten auf FPD-Instance');
            }, 20000);
        }
    }, 500);
    
    // Nach 30 Sekunden aufgeben
    setTimeout(function() {
        clearInterval(waitForFPD);
        debugLog('Timeout beim Warten auf FPD-Container');
    }, 30000);
    
    // FPD Galerie-Integration
    function initFPDGalleryIntegration() {
        // Speichere die Original-Bilder
        saveOriginalGalleryImages();
        
        // Event-Listener registrieren
        setupEventListeners();
        
        // Funktion zum Speichern der Original-Bilder
        function saveOriginalGalleryImages() {
            var $galleryImages = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image');
            originalGalleryImages = [];
            
            $galleryImages.each(function(index) {
                var $img = $(this).find('img');
                var $link = $(this).find('a');
                
                originalGalleryImages.push({
                    index: index,
                    imgSrc: $img.attr('src'),
                    imgDataSrc: $img.attr('data-src'),
                    imgDataLargeImage: $img.attr('data-large_image'),
                    linkHref: $link.attr('href')
                });
                
                debugLog('Original-Bild #' + (index+1) + ' gespeichert', originalGalleryImages[index]);
            });
        }
        
        // Event-Listener für FPD und Galerie
        function setupEventListeners() {
            // Wenn der FPD fertig geladen ist
            $(document).on('productCreate', function() {
                debugLog('Produktkonfigurator geladen');
                // Längere Verzögerung, um sicherzustellen, dass alle Ansichten geladen sind
                setTimeout(function() {
                    captureAllImagesAndReplaceGallery();
                }, 2000);
            });
            
            // Wenn der Benutzer etwas im FPD ändert
            $(document).on('elementModify', function() {
                // Kurze Verzögerung, um sicherzustellen, dass die Änderungen angewendet wurden
                setTimeout(function() {
                    captureAllImagesAndReplaceGallery();
                }, 500);
            });
            
            // Wenn der Benutzer in der Galerie navigiert
            $('.flex-control-nav.flex-control-thumbs li img').on('click', function() {
                var index = $(this).parent().index();
                debugLog('Galerie-Navigation zu Bild #' + (index+1));
                
                // Zur entsprechenden Ansicht im FPD wechseln
                try {
                    if (window.fancyProductDesigner && 
                        window.fancyProductDesigner.viewInstances && 
                        window.fancyProductDesigner.viewInstances.length > index) {
                        window.fancyProductDesigner.selectView(index);
                    }
                } catch (e) {
                    debugLog('Fehler beim Wechseln der FPD-Ansicht', e);
                }
            });
            
            // Dynamisch hinzugefügte Galerie-Elemente überwachen
            $(document).on('click', '.flex-control-nav.flex-control-thumbs li img', function() {
                var index = $(this).parent().index();
                debugLog('Galerie-Navigation zu Bild #' + (index+1) + ' (dynamisch)');
                
                // Zur entsprechenden Ansicht im FPD wechseln
                try {
                    if (window.fancyProductDesigner && 
                        window.fancyProductDesigner.viewInstances && 
                        window.fancyProductDesigner.viewInstances.length > index) {
                        window.fancyProductDesigner.selectView(index);
                    }
                } catch (e) {
                    debugLog('Fehler beim Wechseln der FPD-Ansicht (dynamisch)', e);
                }
            });
            
            // Wenn der FPD geschlossen wird
            $(document).on('modalDesignerClose', function() {
                debugLog('Produktkonfigurator geschlossen, stelle Originalbilder wieder her');
                restoreGalleryImages();
            });
        }
        
        // Hauptfunktion: Erfasst alle Bilder und ersetzt die Galerie
        function captureAllImagesAndReplaceGallery() {
            debugLog('Starte Bilderfassung...');
            
            // Anzahl der Ansichten ermitteln
            var expectedViewCount = 0;
            
            if (window.fancyProductDesigner && window.fancyProductDesigner.viewInstances) {
                expectedViewCount = window.fancyProductDesigner.viewInstances.length;
                debugLog('Anzahl der erwarteten Ansichten: ' + expectedViewCount);
            }
            
            // Verzögerung hinzufügen, um sicherzustellen, dass alle Bilder geladen sind
            setTimeout(function() {
                debugLog('Starte Bilderfassung nach Verzögerung...');
                
                // Methode 1: Direkte Canvas-Erfassung
                var canvasImages = captureCanvasImages();
                
                // Prüfe, ob wir gültige Bilder haben
                var validCanvasImages = canvasImages.filter(function(img) {
                    return img && img.indexOf('data:image') === 0;
                });
                
                if (validCanvasImages.length > 0) {
                    debugLog('Gültige Canvas-Bilder gefunden: ' + validCanvasImages.length);
                    
                    // Wenn wir mehr Bilder haben als erwartet, beschränken wir uns auf die erwartete Anzahl
                    if (expectedViewCount > 0 && validCanvasImages.length > expectedViewCount) {
                        debugLog('Zu viele Canvas-Bilder gefunden, beschränke auf ' + expectedViewCount);
                        validCanvasImages = validCanvasImages.slice(0, expectedViewCount);
                    }
                    
                    // Debug-Ausgabe der Bilder
                    showDebugImages(validCanvasImages);
                    
                    // Galerie ersetzen
                    replaceGalleryWithCanvasImages(validCanvasImages);
                    return;
                }
                
                // Methode 2: Alternative Methode über FPD-API
                debugLog('Keine gültigen Canvas-Bilder gefunden, versuche alternative Methode...');
                captureUsingAlternativeMethod(expectedViewCount);
                
            }, 1500); // 1,5 Sekunden Verzögerung
        }
        
        // Methode 1: Direkte Canvas-Erfassung
        function captureCanvasImages() {
            debugLog('Starte Canvas-Erfassung');
            
            var images = [];
            var canvasSelectors = [
                '.fpd-main-wrapper canvas',
                '.fpd-views-wrapper canvas',
                '.fpd-product-stage canvas',
                '.fpd-mainbar canvas',
                '.fpd-container canvas',
                '.fpd-design-preview canvas',
                '.fpd-views-selection canvas'
            ];
            
            // Alle Canvas-Elemente sammeln
            var allCanvases = [];
            
            for (var i = 0; i < canvasSelectors.length; i++) {
                var canvases = $(canvasSelectors[i]);
                if (canvases.length > 0) {
                    debugLog('Gefunden: ' + canvases.length + ' Canvas-Elemente mit Selektor ' + canvasSelectors[i]);
                    canvases.each(function() {
                        allCanvases.push(this);
                    });
                }
            }
            
            // Duplizierte Canvas-Elemente entfernen
            var uniqueCanvases = [];
            var canvasIds = {};
            
            for (var i = 0; i < allCanvases.length; i++) {
                var canvas = allCanvases[i];
                var canvasId = canvas.id || ('canvas_' + i);
                
                if (!canvasIds[canvasId]) {
                    canvasIds[canvasId] = true;
                    uniqueCanvases.push(canvas);
                }
            }
            
            debugLog('Insgesamt ' + uniqueCanvases.length + ' einzigartige Canvas-Elemente gefunden');
            
            // Bilder aus Canvas-Elementen extrahieren
            for (var i = 0; i < uniqueCanvases.length; i++) {
                try {
                    var canvas = uniqueCanvases[i];
                    
                    // Prüfe, ob das Canvas leer ist
                    var isEmpty = isCanvasEmpty(canvas);
                    
                    if (!isEmpty) {
                        var dataURL = canvas.toDataURL('image/png');
                        
                        // Prüfe, ob das Bild gültig ist
                        if (dataURL && dataURL.indexOf('data:image') === 0) {
                            images.push(dataURL);
                            debugLog('Canvas #' + (i+1) + ' erfolgreich erfasst');
                        } else {
                            debugLog('Ungültiges Bild für Canvas #' + (i+1) + ', überspringe');
                        }
                    } else {
                        debugLog('Canvas #' + (i+1) + ' ist leer, überspringe');
                    }
                } catch (e) {
                    debugLog('Fehler beim Erfassen von Canvas #' + (i+1), e);
                }
            }
            
            debugLog('Canvas-Erfassung abgeschlossen, ' + images.length + ' Bilder gefunden');
            return images;
        }
        
        // Hilfsfunktion: Prüft, ob ein Canvas leer ist
        function isCanvasEmpty(canvas) {
            try {
                var ctx = canvas.getContext('2d');
                var pixelData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
                
                // Prüfe, ob alle Pixel transparent sind
                for (var i = 0; i < pixelData.length; i += 4) {
                    // Wenn Alpha-Kanal nicht 0 ist, ist das Canvas nicht leer
                    if (pixelData[i+3] !== 0) {
                        return false;
                    }
                }
                
                return true;
            } catch (e) {
                // Bei Fehlern (z.B. CORS) nehmen wir an, dass das Canvas nicht leer ist
                debugLog('Fehler beim Prüfen, ob Canvas leer ist', e);
                return false;
            }
        }
        
        // Hilfsfunktion: Bilder über FPD-API erfassen
        function captureUsingFPDAPI(expectedViewCount) {
            var images = [];
            
            try {
                // Methode 1: getViewsDataURL
                if (window.fancyProductDesigner && typeof window.fancyProductDesigner.getViewsDataURL === 'function') {
                    try {
                        debugLog('Versuche Bilder über getViewsDataURL zu erfassen');
                        var apiImages = window.fancyProductDesigner.getViewsDataURL();
                        
                        if (apiImages && apiImages.length > 0) {
                            // Prüfe, ob die Bilder gültig sind
                            var validImages = [];
                            for (var i = 0; i < apiImages.length; i++) {
                                if (apiImages[i] && apiImages[i].indexOf('data:image') === 0) {
                                    validImages.push(apiImages[i]);
                                    debugLog('Gültiges Bild #' + (i+1) + ' über getViewsDataURL gefunden');
                                } else {
                                    debugLog('Ungültiges Bild #' + (i+1) + ' über getViewsDataURL gefunden, überspringe');
                                }
                            }
                            
                            // Wenn wir mehr Bilder haben als erwartet, filtern wir die überschüssigen heraus
                            if (expectedViewCount > 0 && validImages.length > expectedViewCount) {
                                debugLog('Zu viele API-Bilder gefunden (' + validImages.length + '), reduziere auf ' + expectedViewCount);
                                validImages = validImages.slice(0, expectedViewCount);
                            }
                            
                            debugLog('Bilder über getViewsDataURL erfolgreich abgerufen: ' + validImages.length);
                            return validImages;
                        }
                    } catch (e) {
                        debugLog('Fehler bei getViewsDataURL', e);
                    }
                }
                
                // Methode 2: Einzelne viewInstances
                if (window.fancyProductDesigner && window.fancyProductDesigner.viewInstances) {
                    var viewInstances = window.fancyProductDesigner.viewInstances;
                    
                    debugLog('Versuche ' + viewInstances.length + ' Ansichten direkt zu erfassen');
                    
                    for (var i = 0; i < viewInstances.length; i++) {
                        if (viewInstances[i]) {
                            try {
                                debugLog('Erfasse Ansicht ' + i);
                                var dataURL = viewInstances[i].toDataURL();
                                
                                // Prüfe, ob das Bild gültig ist
                                if (dataURL && dataURL.indexOf('data:image') === 0) {
                                    images.push(dataURL);
                                    debugLog('Ansicht ' + i + ' erfolgreich erfasst');
                                } else {
                                    debugLog('Ungültiges Bild für Ansicht ' + i + ' gefunden, überspringe');
                                }
                            } catch (e) {
                                debugLog('Fehler beim Erfassen der Ansicht ' + i, e);
                            }
                        }
                    }
                    
                    if (images.length > 0) {
                        debugLog('Bilder über viewInstances erfolgreich abgerufen: ' + images.length);
                        return images;
                    }
                }
                
                return images;
            } catch (e) {
                debugLog('Fehler bei der API-Bilderfassung', e);
                return [];
            }
        }
        
        // Methode 2: Alternative Methode über FPD-API
        function captureUsingAlternativeMethod(expectedViewCount) {
            try {
                debugLog('Versuche alternative Methode über FPD-API');
                
                // Zuerst versuchen wir es mit der API-Methode
                var apiImages = captureUsingFPDAPI(expectedViewCount);
                if (apiImages.length > 0) {
                    replaceGalleryWithCanvasImages(apiImages);
                    return;
                }
                
                // Methode 3: currentViewInstance
                if (window.fancyProductDesigner && window.fancyProductDesigner.currentViewInstance) {
                    try {
                        var dataURL = window.fancyProductDesigner.currentViewInstance.toDataURL();
                        
                        // Prüfe, ob das Bild gültig ist
                        if (dataURL && dataURL.indexOf('data:image') === 0) {
                            var images = [dataURL];
                            debugLog('Bild über currentViewInstance erfolgreich abgerufen');
                            showDebugImages(images);
                            replaceGalleryWithCanvasImages(images);
                            return;
                        } else {
                            debugLog('Ungültiges Bild über currentViewInstance erhalten, überspringe');
                        }
                    } catch (e) {
                        debugLog('Fehler bei currentViewInstance', e);
                    }
                }
                
                // Methode 4: DOM-Manipulation
                var $mainImage = $('.fpd-main-wrapper img');
                if ($mainImage.length > 0) {
                    try {
                        // Erstelle ein Canvas-Element und zeichne das Bild darauf
                        var canvas = document.createElement('canvas');
                        var ctx = canvas.getContext('2d');
                        var img = new Image();
                        
                        img.onload = function() {
                            canvas.width = img.width;
                            canvas.height = img.height;
                            ctx.drawImage(img, 0, 0);
                            
                            try {
                                var dataURL = canvas.toDataURL('image/png');
                                
                                // Prüfe, ob das Bild gültig ist
                                if (dataURL && dataURL.indexOf('data:image') === 0) {
                                    var images = [dataURL];
                                    debugLog('Bild über DOM-Manipulation erfolgreich abgerufen');
                                    showDebugImages(images);
                                    replaceGalleryWithCanvasImages(images);
                                } else {
                                    debugLog('Ungültiges Bild über DOM-Manipulation erhalten, überspringe');
                                }
                            } catch (e) {
                                debugLog('Fehler bei Canvas-Konvertierung', e);
                            }
                        };
                        
                        img.src = $mainImage.attr('src');
                    } catch (e) {
                        debugLog('Fehler bei DOM-Manipulation', e);
                    }
                }
                
                // Methode 5: Versuche, Thumbnails zu erfassen
                var $thumbnails = $('.fpd-views-selection img');
                if ($thumbnails.length > 0) {
                    try {
                        var images = [];
                        
                        $thumbnails.each(function(index) {
                            var src = $(this).attr('src');
                            if (src && src.indexOf('data:image') === 0) {
                                images.push(src);
                                debugLog('Thumbnail ' + index + ' erfolgreich erfasst');
                            } else {
                                debugLog('Ungültiges Thumbnail ' + index + ' gefunden, überspringe');
                            }
                        });
                        
                        if (images.length > 0) {
                            debugLog('Bilder über Thumbnails erfolgreich abgerufen: ' + images.length);
                            showDebugImages(images);
                            replaceGalleryWithCanvasImages(images);
                            return;
                        }
                    } catch (e) {
                        debugLog('Fehler bei Thumbnail-Erfassung', e);
                    }
                }
                
                // Methode 6: Versuche, Bilder aus dem FPD-Container zu extrahieren
                var $fpdImages = $('.fpd-container img');
                if ($fpdImages.length > 0) {
                    try {
                        var images = [];
                        
                        $fpdImages.each(function(index) {
                            var src = $(this).attr('src');
                            if (src && src.indexOf('data:image') === 0) {
                                images.push(src);
                                debugLog('FPD-Container Bild ' + index + ' erfolgreich erfasst');
                            } else if (src) {
                                // Versuche, das Bild zu laden und in ein Canvas zu konvertieren
                                try {
                                    var img = new Image();
                                    img.crossOrigin = "Anonymous";
                                    img.onload = function() {
                                        var canvas = document.createElement('canvas');
                                        canvas.width = img.width;
                                        canvas.height = img.height;
                                        var ctx = canvas.getContext('2d');
                                        ctx.drawImage(img, 0, 0);
                                        
                                        try {
                                            var dataURL = canvas.toDataURL('image/png');
                                            if (dataURL && dataURL.indexOf('data:image') === 0) {
                                                images.push(dataURL);
                                                debugLog('FPD-Container Bild ' + index + ' erfolgreich konvertiert');
                                                
                                                // Wenn wir genug Bilder haben, aktualisiere die Galerie
                                                if (images.length === expectedViewCount || (expectedViewCount === 0 && images.length > 0)) {
                                                    showDebugImages(images);
                                                    replaceGalleryWithCanvasImages(images);
                                                }
                                            }
                                        } catch (e) {
                                            debugLog('Fehler bei Canvas-Konvertierung für FPD-Container Bild ' + index, e);
                                        }
                                    };
                                    img.src = src;
                                } catch (e) {
                                    debugLog('Fehler beim Laden von FPD-Container Bild ' + index, e);
                                }
                            }
                        });
                        
                        if (images.length > 0) {
                            debugLog('Bilder aus FPD-Container erfolgreich abgerufen: ' + images.length);
                            showDebugImages(images);
                            replaceGalleryWithCanvasImages(images);
                            return;
                        }
                    } catch (e) {
                        debugLog('Fehler bei FPD-Container-Bilderfassung', e);
                    }
                }
                
                debugLog('Alle Methoden zum Abrufen der Bilder fehlgeschlagen');
            } catch (e) {
                debugLog('Kritischer Fehler in der alternativen Methode', e);
            }
        }
        
        // Galerie mit Canvas-Bildern ersetzen
        function replaceGalleryWithCanvasImages(images) {
            if (!images || images.length === 0) {
                debugLog('Keine Bilder zum Ersetzen der Galerie vorhanden');
                return;
            }
            
            debugLog('Ersetze Galerie mit ' + images.length + ' Bildern');
            
            // Filtere ungültige Bilder heraus
            var validImages = [];
            for (var i = 0; i < images.length; i++) {
                if (images[i] && images[i].indexOf('data:image') === 0) {
                    validImages.push(images[i]);
                } else {
                    debugLog('Bild #' + (i+1) + ' ist ungültig und wird übersprungen');
                }
            }
            
            if (validImages.length === 0) {
                debugLog('Keine gültigen Bilder zum Ersetzen der Galerie vorhanden');
                return;
            }
            
            debugLog('Nach Filterung: ' + validImages.length + ' gültige Bilder');
            
            // Finde die WooCommerce-Galerie
            var $gallery = $('.woocommerce-product-gallery');
            
            if ($gallery.length === 0) {
                debugLog('WooCommerce-Galerie nicht gefunden');
                return;
            }
            
            // Finde die Galerie-Elemente
            var $galleryImages = $gallery.find('.woocommerce-product-gallery__image');
            var $galleryWrapper = $gallery.find('.flex-viewport');
            var $galleryThumbnails = $gallery.find('.flex-control-thumbs');
            
            if ($galleryImages.length === 0) {
                debugLog('Keine Galerie-Bilder gefunden');
                return;
            }
            
            debugLog('Gefunden: ' + $galleryImages.length + ' Galerie-Bilder');
            
            // Ersetze die Hauptbilder
            $galleryImages.each(function(index) {
                if (index < validImages.length) {
                    var $img = $(this).find('img');
                    var $a = $(this).find('a');
                    
                    if ($img.length > 0) {
                        $img.attr('src', validImages[index]);
                        $img.attr('data-src', validImages[index]);
                        $img.attr('data-large_image', validImages[index]);
                        $img.attr('srcset', '');
                        debugLog('Hauptbild #' + (index+1) + ' ersetzt');
                    }
                    
                    if ($a.length > 0) {
                        $a.attr('href', validImages[index]);
                        debugLog('Link für Bild #' + (index+1) + ' ersetzt');
                    }
                }
            });
            
            // Ersetze die Thumbnails
            if ($galleryThumbnails.length > 0) {
                var $thumbs = $galleryThumbnails.find('li img');
                
                if ($thumbs.length > 0) {
                    debugLog('Gefunden: ' + $thumbs.length + ' Thumbnail-Bilder');
                    
                    $thumbs.each(function(index) {
                        if (index < validImages.length) {
                            $(this).attr('src', validImages[index]);
                            debugLog('Thumbnail #' + (index+1) + ' ersetzt');
                        }
                    });
                }
            }
            
            // Wenn wir weniger Bilder haben als in der Galerie, blenden wir die überschüssigen aus
            if (validImages.length < $galleryImages.length) {
                for (var i = validImages.length; i < $galleryImages.length; i++) {
                    $($galleryImages[i]).hide();
                    debugLog('Überschüssiges Galerie-Bild #' + (i+1) + ' ausgeblendet');
                }
            }
            
            // Wenn wir Thumbnails haben, aber weniger als in der Galerie, blenden wir die überschüssigen aus
            if ($galleryThumbnails.length > 0) {
                var $thumbs = $galleryThumbnails.find('li');
                
                if (validImages.length < $thumbs.length) {
                    for (var i = validImages.length; i < $thumbs.length; i++) {
                        $($thumbs[i]).hide();
                        debugLog('Überschüssiges Thumbnail #' + (i+1) + ' ausgeblendet');
                    }
                }
            }
            
            // Trigger resize-Event, um sicherzustellen, dass die Galerie korrekt angezeigt wird
            setTimeout(function() {
                $(window).trigger('resize');
                debugLog('Resize-Event ausgelöst');
            }, 100);
            
            debugLog('Galerie erfolgreich ersetzt');
        }
        
        // Originalbilder wiederherstellen
        function restoreGalleryImages() {
            try {
                var $galleryImages = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image');
                
                // Jedes Bild mit dem Original ersetzen
                for (var i = 0; i < originalGalleryImages.length; i++) {
                    var original = originalGalleryImages[i];
                    var $currentImage = $galleryImages.eq(original.index);
                    
                    if ($currentImage.length) {
                        var $img = $currentImage.find('img');
                        var $link = $currentImage.find('a');
                        
                        // Original-Bilder wiederherstellen
                        $img.attr('src', original.imgSrc);
                        $img.attr('data-src', original.imgDataSrc);
                        $img.attr('data-large_image', original.imgDataLargeImage);
                        $link.attr('href', original.linkHref);
                        
                        debugLog('Originalbild #' + (original.index+1) + ' wiederhergestellt');
                    }
                }
                
                // Galerie aktualisieren
                if (typeof $galleryImages.data('flexslider') !== 'undefined') {
                    $galleryImages.data('flexslider').resize();
                }
                
                // Wenn Photoswipe oder andere Lightbox verwendet wird, Event auslösen
                $('body').trigger('wc-product-gallery-after-init');
                
                debugLog('Alle Originalbilder in der Galerie wiederhergestellt');
            } catch (e) {
                debugLog('Fehler beim Wiederherstellen der Originalbilder', e);
            }
        }
        
        // Debug-Funktion zum Anzeigen der Bilder
        function showDebugImages(images) {
            if (!debugMode) return;
            
            // Anzahl der erwarteten Ansichten ermitteln
            var expectedViewCount = 0;
            if (window.fancyProductDesigner && window.fancyProductDesigner.viewInstances) {
                expectedViewCount = window.fancyProductDesigner.viewInstances.length;
            }
            
            // Wenn wir mehr Bilder haben als erwartet, filtern wir die überschüssigen heraus
            if (expectedViewCount > 0 && images.length > expectedViewCount) {
                debugLog('Debug: Zu viele Bilder für Debug-Anzeige (' + images.length + '), reduziere auf ' + expectedViewCount);
                images = images.slice(0, expectedViewCount);
            }
            
            console.log('FPD DEBUG: ' + images.length + ' Bilder erfasst:');
            
            // Erstelle ein Debug-Element auf der Seite, wenn es noch nicht existiert
            if (!$('#fpd-debug-output').length) {
                $('body').append('<div id="fpd-debug-output" style="position: fixed; bottom: 10px; right: 10px; background: white; border: 1px solid #ccc; padding: 15px; z-index: 9999; width: 600px; max-height: 500px; overflow: auto; box-shadow: 0 0 10px rgba(0,0,0,0.2);"><h3 style="margin-top: 0;">FPD Debug: Erfasste Bilder (' + images.length + ')</h3><div id="fpd-debug-images"></div><div style="margin-top: 15px; text-align: right;"><button id="fpd-debug-close" style="padding: 5px 10px; background: #f44336; color: white; border: none; cursor: pointer;">Schließen</button></div></div>');
                
                // Schließen-Button
                $('#fpd-debug-close').on('click', function() {
                    $('#fpd-debug-output').remove();
                });
            } else {
                // Aktualisiere die Überschrift mit der aktuellen Anzahl der Bilder
                $('#fpd-debug-output h3').text('FPD Debug: Erfasste Bilder (' + images.length + ')');
            }
            
            // Debug-Element leeren
            $('#fpd-debug-images').empty();
            
            // Bilder hinzufügen
            for (var i = 0; i < images.length; i++) {
                if (images[i]) {
                    // Überprüfe, ob das Bild gültig ist
                    var isValidImage = images[i].indexOf('data:image') === 0;
                    var imgHtml = isValidImage 
                        ? '<img src="' + images[i] + '" style="max-width: 100%; height: auto; border: 1px solid #ddd;" />'
                        : '<div style="width: 100%; height: 150px; background: #f5f5f5; display: flex; justify-content: center; align-items: center; border: 1px solid #ddd;">Ungültiges Bild</div>';
                    
                    // Erstelle einen kurzen Preview-String für die Anzeige (nur die ersten 50 Zeichen)
                    var previewString = images[i].substring(0, 50) + '...';
                    
                    $('#fpd-debug-images').append(
                        '<div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">' +
                        '<strong style="font-size: 16px; display: block; margin-bottom: 10px;">Bild #' + (i+1) + (isValidImage ? '' : ' (Ungültig)') + '</strong>' +
                        '<div style="display: flex; align-items: start;">' +
                        '<div style="flex: 0 0 200px; margin-right: 15px;">' + imgHtml + '</div>' +
                        '<div style="flex: 1;"><textarea style="width: 100%; height: 150px; font-family: monospace; font-size: 12px; padding: 5px;">' + previewString + '</textarea></div>' +
                        '</div></div>'
                    );
                }
            }
        }
    }
});
