/**
 * FPD Galerie-Integration
 * Ersetzt alle Bilder in der WooCommerce-Galerie mit FPD-Bildern
 * Version: 1.8.6
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
            try {
                var $gallery = $('.woocommerce-product-gallery');
                
                if ($gallery.length === 0) {
                    debugLog('WooCommerce-Galerie nicht gefunden');
                    return;
                }
                
                var $galleryImages = $gallery.find('.woocommerce-product-gallery__image');
                
                if ($galleryImages.length === 0) {
                    debugLog('Keine Galeriebilder gefunden');
                    return;
                }
                
                debugLog('Speichere ' + $galleryImages.length + ' ursprüngliche Galeriebilder');
                
                originalGalleryImages = [];
                
                $galleryImages.each(function(index) {
                    var $img = $(this).find('img');
                    var $a = $(this).find('a');
                    
                    var imgSrc = '';
                    var imgDataSrc = '';
                    var imgLargeImage = '';
                    var aHref = '';
                    
                    if ($img.length > 0) {
                        imgSrc = $img.attr('src') || '';
                        imgDataSrc = $img.attr('data-src') || '';
                        imgLargeImage = $img.attr('data-large_image') || '';
                    }
                    
                    if ($a.length > 0) {
                        aHref = $a.attr('href') || '';
                    }
                    
                    // Wähle die beste verfügbare Quelle
                    var bestSource = '';
                    
                    if (imgLargeImage && imgLargeImage.length > 0) {
                        bestSource = imgLargeImage;
                    } else if (aHref && aHref.length > 0) {
                        bestSource = aHref;
                    } else if (imgDataSrc && imgDataSrc.length > 0) {
                        bestSource = imgDataSrc;
                    } else if (imgSrc && imgSrc.length > 0) {
                        bestSource = imgSrc;
                    }
                    
                    originalGalleryImages.push({
                        index: index,
                        imgSrc: bestSource,
                        imgDataSrc: imgDataSrc,
                        imgLargeImage: imgLargeImage,
                        aHref: aHref
                    });
                    
                    debugLog('Originalbild #' + (index+1) + ' gespeichert: ' + bestSource);
                });
                
                debugLog('Ursprüngliche Galeriebilder gespeichert: ' + originalGalleryImages.length);
            } catch (e) {
                debugLog('Fehler beim Speichern der ursprünglichen Galeriebilder', e);
            }
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
        
        // Erfasse Canvas-Bilder
        function captureCanvasImages() {
            try {
                debugLog('Starte Canvas-Erfassung');
                
                // Finde alle Canvas-Elemente
                var canvasElements = document.querySelectorAll('canvas');
                debugLog('Gefunden: ' + canvasElements.length + ' Canvas-Elemente');
                
                // Speichere die Anzahl der Originalbilder in der Galerie
                var originalGalleryCount = originalGalleryImages.length;
                debugLog('Anzahl der Originalbilder in der Galerie: ' + originalGalleryCount);
                
                // Bestimme die erwartete Anzahl der Ansichten
                var expectedViewCount = 0;
                if (window.fancyProductDesigner && window.fancyProductDesigner.viewInstances) {
                    expectedViewCount = window.fancyProductDesigner.viewInstances.length;
                    debugLog('Erwartete Anzahl der Ansichten: ' + expectedViewCount);
                }
                
                // Sammle alle gültigen Canvas-Bilder
                var images = [];
                var validCanvasCount = 0;
                
                for (var i = 0; i < canvasElements.length; i++) {
                    var canvas = canvasElements[i];
                    
                    // Prüfe, ob das Canvas eine gültige Größe hat
                    if (canvas.width <= 0 || canvas.height <= 0) {
                        debugLog('Canvas #' + (i+1) + ' hat keine gültige Größe: ' + canvas.width + 'x' + canvas.height + ', überspringe');
                        continue;
                    }
                    
                    // Prüfe, ob das Canvas leer ist
                    if (isCanvasEmpty(canvas)) {
                        debugLog('Canvas #' + (i+1) + ' ist leer, überspringe');
                        continue;
                    }
                    
                    // Versuche, das Canvas in eine Daten-URL zu konvertieren
                    try {
                        var dataURL = canvas.toDataURL('image/png');
                        
                        // Prüfe, ob die Daten-URL gültig ist
                        if (dataURL && dataURL.indexOf('data:image') === 0) {
                            // Prüfe, ob die Daten-URL eine angemessene Länge hat
                            if (dataURL.length < 5000) {
                                debugLog('Canvas #' + (i+1) + ' hat verdächtig kurze Daten-URL: ' + dataURL.length + ' Zeichen, überspringe');
                                continue;
                            }
                            
                            images.push(dataURL);
                            validCanvasCount++;
                            debugLog('Canvas #' + (i+1) + ' erfolgreich erfasst');
                        } else {
                            debugLog('Canvas #' + (i+1) + ' hat ungültige Daten-URL, überspringe');
                        }
                    } catch (e) {
                        debugLog('Fehler beim Konvertieren von Canvas #' + (i+1) + ' in Daten-URL', e);
                    }
                }
                
                debugLog('Canvas-Erfassung abgeschlossen, ' + validCanvasCount + ' Bilder gefunden');
                
                // Wenn wir keine Bilder gefunden haben, versuche alternative Methoden
                if (images.length === 0) {
                    debugLog('Keine Canvas-Bilder gefunden, versuche alternative Methoden');
                    captureImagesAlternative();
                    return;
                }
                
                // Wenn wir weniger Bilder haben als erwartet, versuche, die fehlenden Bilder zu finden
                if (expectedViewCount > 0 && images.length < expectedViewCount) {
                    debugLog('Weniger Bilder als erwartet gefunden (' + images.length + ' vs ' + expectedViewCount + '), versuche fehlende Bilder zu finden');
                    
                    // Versuche, die fehlenden Bilder aus dem FPD-API zu bekommen
                    if (window.fancyProductDesigner) {
                        try {
                            var viewInstances = window.fancyProductDesigner.viewInstances;
                            
                            for (var i = images.length; i < expectedViewCount; i++) {
                                if (i < viewInstances.length) {
                                    var viewInstance = viewInstances[i];
                                    
                                    if (viewInstance && viewInstance.toDataURL) {
                                        try {
                                            var viewDataURL = viewInstance.toDataURL({format: 'png'});
                                            
                                            if (viewDataURL && viewDataURL.indexOf('data:image') === 0) {
                                                // Prüfe, ob die Daten-URL eine angemessene Länge hat
                                                if (viewDataURL.length < 5000) {
                                                    debugLog('Ansicht #' + (i+1) + ' hat verdächtig kurze Daten-URL: ' + viewDataURL.length + ' Zeichen, überspringe');
                                                    
                                                    // Verwende stattdessen das Originalbild, falls verfügbar
                                                    if (i < originalGalleryCount) {
                                                        var originalImage = originalGalleryImages[i];
                                                        if (originalImage && originalImage.imgSrc) {
                                                            debugLog('Verwende Originalbild für Ansicht #' + (i+1));
                                                            images.push(originalImage.imgSrc);
                                                        }
                                                    }
                                                    
                                                    continue;
                                                }
                                                
                                                images.push(viewDataURL);
                                                debugLog('Ansicht #' + (i+1) + ' erfolgreich über FPD-API erfasst');
                                            }
                                        } catch (e) {
                                            debugLog('Fehler beim Erfassen der Ansicht #' + (i+1) + ' über FPD-API', e);
                                            
                                            // Verwende stattdessen das Originalbild, falls verfügbar
                                            if (i < originalGalleryCount) {
                                                var originalImage = originalGalleryImages[i];
                                                if (originalImage && originalImage.imgSrc) {
                                                    debugLog('Verwende Originalbild für Ansicht #' + (i+1) + ' nach Fehler');
                                                    images.push(originalImage.imgSrc);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (e) {
                            debugLog('Fehler beim Zugriff auf FPD-API', e);
                        }
                    }
                }
                
                // Wenn wir immer noch weniger Bilder haben als erwartet, fülle mit Originalbildern auf
                if (expectedViewCount > 0 && images.length < expectedViewCount) {
                    debugLog('Immer noch weniger Bilder als erwartet (' + images.length + ' vs ' + expectedViewCount + '), fülle mit Originalbildern auf');
                    
                    for (var i = images.length; i < expectedViewCount; i++) {
                        if (i < originalGalleryCount) {
                            var originalImage = originalGalleryImages[i];
                            if (originalImage && originalImage.imgSrc) {
                                debugLog('Füge Originalbild für Position #' + (i+1) + ' hinzu');
                                images.push(originalImage.imgSrc);
                            }
                        }
                    }
                }
                
                // Wenn wir genügend Bilder haben, zeige sie an und ersetze die Galerie
                if (images.length > 0) {
                    debugLog('Gültige Canvas-Bilder gefunden: ' + images.length);
                    debugLog(images.length + ' Bilder erfasst: ', images);
                    showDebugImages(images);
                    replaceGalleryWithCanvasImages(images);
                } else {
                    debugLog('Keine gültigen Canvas-Bilder gefunden');
                }
            } catch (e) {
                debugLog('Fehler bei der Canvas-Erfassung', e);
            }
        }
        
        // Hilfsfunktion: Prüft, ob ein Canvas leer ist
        function isCanvasEmpty(canvas) {
            try {
                // Prüfe, ob das Canvas-Element gültig ist
                if (!canvas || !canvas.getContext || typeof canvas.getContext !== 'function') {
                    debugLog('Ungültiges Canvas-Element');
                    return false;
                }
                
                // Prüfe, ob das Canvas eine Größe hat
                if (canvas.width <= 0 || canvas.height <= 0) {
                    debugLog('Canvas hat keine gültige Größe: ' + canvas.width + 'x' + canvas.height);
                    return false;
                }
                
                // Versuche, die Bilddaten zu bekommen
                try {
                    var ctx = canvas.getContext('2d');
                    var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    var data = imageData.data;
                    
                    // Prüfe, ob alle Pixel transparent sind
                    for (var i = 0; i < data.length; i += 4) {
                        // Wenn der Alpha-Kanal nicht 0 ist, ist das Canvas nicht leer
                        if (data[i + 3] !== 0) {
                            return false;
                        }
                    }
                    
                    // Wenn wir hier ankommen, sind alle Pixel transparent
                    return true;
                } catch (e) {
                    // Wenn wir einen CORS-Fehler bekommen, versuchen wir eine alternative Methode
                    if (e.name === 'SecurityError' || e.message.indexOf('cross-origin') !== -1) {
                        debugLog('CORS-Fehler beim Prüfen des Canvas, versuche alternative Methode');
                        
                        try {
                            // Alternative Methode: Konvertiere das Canvas in eine Daten-URL und prüfe die Größe
                            var dataURL = canvas.toDataURL('image/png');
                            
                            // Ein leeres Canvas erzeugt typischerweise eine sehr kleine Daten-URL
                            // (weniger als 100 Zeichen für ein komplett transparentes Bild)
                            if (dataURL.length < 5000) {
                                debugLog('Canvas scheint leer zu sein (kleine Daten-URL: ' + dataURL.length + ' Zeichen)');
                                return true;
                            }
                            
                            // Wenn die Daten-URL größer ist, enthält das Canvas wahrscheinlich Daten
                            return false;
                        } catch (e2) {
                            debugLog('Fehler bei der alternativen Canvas-Prüfung', e2);
                            // Im Zweifelsfall nehmen wir an, dass das Canvas nicht leer ist
                            return false;
                        }
                    }
                    
                    debugLog('Fehler beim Prüfen des Canvas', e);
                    // Im Zweifelsfall nehmen wir an, dass das Canvas nicht leer ist
                    return false;
                }
            } catch (e) {
                debugLog('Unerwarteter Fehler beim Prüfen des Canvas', e);
                // Im Zweifelsfall nehmen wir an, dass das Canvas nicht leer ist
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
                    
                    // Erfasse die aktuelle Ansicht zuerst
                    var currentViewIndex = window.fancyProductDesigner.currentViewIndex;
                    debugLog('Aktuelle Ansicht: ' + currentViewIndex);
                    
                    // Sortiere die Ansichten nach Index
                    var sortedViewIndices = [];
                    for (var i = 0; i < viewInstances.length; i++) {
                        sortedViewIndices.push(i);
                    }
                    
                    // Erfasse die Bilder in der richtigen Reihenfolge
                    for (var i = 0; i < sortedViewIndices.length; i++) {
                        var viewIndex = sortedViewIndices[i];
                        if (viewInstances[viewIndex]) {
                            try {
                                debugLog('Erfasse Ansicht ' + viewIndex);
                                var dataURL = viewInstances[viewIndex].toDataURL();
                                
                                // Prüfe, ob das Bild gültig ist
                                if (dataURL && dataURL.indexOf('data:image') === 0) {
                                    images.push(dataURL);
                                    debugLog('Ansicht ' + viewIndex + ' erfolgreich erfasst');
                                } else {
                                    debugLog('Ungültiges Bild für Ansicht ' + viewIndex + ' gefunden, überspringe');
                                }
                            } catch (e) {
                                debugLog('Fehler beim Erfassen der Ansicht ' + viewIndex, e);
                                
                                // Versuche es mit einer alternativen Methode für diese Ansicht
                                try {
                                    debugLog('Versuche alternative Methode für Ansicht ' + viewIndex);
                                    var stage = viewInstances[viewIndex].stage;
                                    if (stage && stage.toDataURL) {
                                        var stageDataURL = stage.toDataURL();
                                        if (stageDataURL && stageDataURL.indexOf('data:image') === 0) {
                                            images.push(stageDataURL);
                                            debugLog('Ansicht ' + viewIndex + ' erfolgreich über Stage erfasst');
                                        }
                                    }
                                } catch (e2) {
                                    debugLog('Fehler bei alternativer Methode für Ansicht ' + viewIndex, e2);
                                }
                            }
                        }
                    }
                    
                    if (images.length > 0) {
                        debugLog('Bilder über viewInstances erfolgreich abgerufen: ' + images.length);
                        return images;
                    }
                }
                
                // Methode 3: Versuche, die Bilder über getProductDataURL zu erfassen
                if (window.fancyProductDesigner && typeof window.fancyProductDesigner.getProductDataURL === 'function') {
                    try {
                        debugLog('Versuche Bilder über getProductDataURL zu erfassen');
                        var productDataURL = window.fancyProductDesigner.getProductDataURL();
                        
                        if (productDataURL && typeof productDataURL === 'string' && productDataURL.indexOf('data:image') === 0) {
                            debugLog('Bild über getProductDataURL erfolgreich abgerufen');
                            return [productDataURL];
                        } else if (productDataURL && Array.isArray(productDataURL)) {
                            var validProductImages = [];
                            for (var i = 0; i < productDataURL.length; i++) {
                                if (productDataURL[i] && productDataURL[i].indexOf('data:image') === 0) {
                                    validProductImages.push(productDataURL[i]);
                                }
                            }
                            
                            if (validProductImages.length > 0) {
                                debugLog('Bilder über getProductDataURL erfolgreich abgerufen: ' + validProductImages.length);
                                return validProductImages;
                            }
                        }
                    } catch (e) {
                        debugLog('Fehler bei getProductDataURL', e);
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
                if (images[i] && typeof images[i] === 'string' && images[i].indexOf('data:image') === 0) {
                    // Prüfe, ob die Daten-URL vollständig ist (mindestens 100 Zeichen)
                    if (images[i].length < 100) {
                        debugLog('Bild #' + (i+1) + ' hat verdächtig kurze Daten-URL: ' + images[i].length + ' Zeichen, überspringe');
                        continue;
                    }
                    
                    validImages.push(images[i]);
                    debugLog('Bild #' + (i+1) + ' ist gültig (Länge: ' + images[i].length + ')');
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
            
            // Speichere die Anzahl der ursprünglichen Galeriebilder
            var originalGalleryCount = $galleryImages.length;
            debugLog('Ursprüngliche Anzahl der Galeriebilder: ' + originalGalleryCount);
            
            // Wenn wir weniger Bilder haben als ursprünglich in der Galerie, füllen wir mit den Originalbildern auf
            if (validImages.length < originalGalleryCount) {
                debugLog('Weniger Bilder als ursprünglich (' + validImages.length + ' vs ' + originalGalleryCount + '), fülle mit Originalbildern auf');
                
                // Prüfe, ob wir Originalbilder haben
                if (!originalGalleryImages || originalGalleryImages.length === 0) {
                    debugLog('Keine Originalbilder zum Auffüllen vorhanden');
                } else {
                    // Wir verwenden nur so viele Originalbilder wie nötig, um die Galerie zu füllen
                    for (var i = validImages.length; i < originalGalleryCount; i++) {
                        if (i < originalGalleryImages.length) {
                            var originalImage = originalGalleryImages[i];
                            if (originalImage && originalImage.imgSrc) {
                                debugLog('Füge Originalbild #' + (i+1) + ' zur Galerie hinzu');
                                validImages.push(originalImage.imgSrc);
                            } else {
                                debugLog('Originalbild #' + (i+1) + ' ist ungültig, kann nicht hinzugefügt werden');
                                
                                // Versuche, das Bild aus dem DOM zu extrahieren
                                var $originalImg = $galleryImages.eq(i).find('img');
                                if ($originalImg.length > 0) {
                                    var originalSrc = $originalImg.attr('src');
                                    if (originalSrc) {
                                        debugLog('Verwende DOM-Bild für Position #' + (i+1));
                                        validImages.push(originalSrc);
                                    }
                                }
                            }
                        }
                    }
                }
                
                debugLog('Nach Auffüllung: ' + validImages.length + ' Bilder');
            }
            
            // Prüfe jedes Bild auf Gültigkeit
            for (var i = 0; i < validImages.length; i++) {
                // Prüfe, ob das Bild eine gültige Daten-URL ist
                if (validImages[i].indexOf('data:image') === 0) {
                    // Prüfe, ob die Daten-URL vollständig ist
                    // Ein typisches Bild hat mindestens 10.000 Zeichen
                    // Wenn das Bild weniger als 10.000 Zeichen hat, ist es wahrscheinlich beschädigt
                    if (validImages[i].length < 10000) {
                        debugLog('Bild #' + (i+1) + ' hat verdächtig kurze Daten-URL: ' + validImages[i].length + ' Zeichen');
                        
                        // Ersetze das ungültige Bild mit dem Originalbild, falls verfügbar
                        if (i < originalGalleryImages.length && originalGalleryImages[i] && originalGalleryImages[i].imgSrc) {
                            debugLog('Ersetze ungültiges Bild #' + (i+1) + ' mit Originalbild');
                            validImages[i] = originalGalleryImages[i].imgSrc;
                        }
                    }
                }
            }
            
            // Ersetze die Hauptbilder
            $galleryImages.each(function(index) {
                if (index < validImages.length) {
                    var $img = $(this).find('img');
                    var $a = $(this).find('a');
                    
                    // Prüfe, ob das Bild gültig ist
                    var currentImage = validImages[index];
                    var isValidImage = currentImage && typeof currentImage === 'string';
                    var isDataUrl = isValidImage && currentImage.indexOf('data:image') === 0;
                    var isDataUrlValid = isDataUrl && currentImage.length >= 10000;
                    var isHttpUrl = isValidImage && (currentImage.indexOf('http://') === 0 || currentImage.indexOf('https://') === 0);
                    
                    // Wenn es eine Daten-URL ist, aber zu kurz, versuche das Originalbild zu verwenden
                    if (isDataUrl && !isDataUrlValid) {
                        debugLog('Bild #' + (index+1) + ' hat ungültige Daten-URL (zu kurz), versuche Originalbild');
                        
                        if (index < originalGalleryImages.length && originalGalleryImages[index] && originalGalleryImages[index].imgSrc) {
                            currentImage = originalGalleryImages[index].imgSrc;
                            isValidImage = true;
                            isDataUrl = false;
                            isHttpUrl = currentImage.indexOf('http://') === 0 || currentImage.indexOf('https://') === 0;
                            debugLog('Bild #' + (index+1) + ' durch Originalbild ersetzt: ' + currentImage);
                        }
                    }
                    
                    if (isValidImage && (isDataUrlValid || isHttpUrl)) {
                        if ($img.length > 0) {
                            $img.attr('src', currentImage);
                            $img.attr('data-src', currentImage);
                            $img.attr('data-large_image', currentImage);
                            $img.attr('srcset', '');
                            debugLog('Hauptbild #' + (index+1) + ' ersetzt' + (isDataUrl ? ' (Daten-URL)' : ' (HTTP-URL)'));
                        }
                        
                        if ($a.length > 0) {
                            $a.attr('href', currentImage);
                            debugLog('Link für Bild #' + (index+1) + ' ersetzt');
                        }
                        
                        // Stelle sicher, dass das Bild sichtbar ist
                        $(this).show();
                    } else {
                        debugLog('Ungültiges Bild für Position #' + (index+1) + ', verwende Original');
                        
                        // Versuche, das Originalbild zu verwenden
                        if (index < originalGalleryImages.length && originalGalleryImages[index] && originalGalleryImages[index].imgSrc) {
                            var originalSrc = originalGalleryImages[index].imgSrc;
                            
                            if ($img.length > 0) {
                                $img.attr('src', originalSrc);
                                $img.attr('data-src', originalSrc);
                                $img.attr('data-large_image', originalSrc);
                                $a.attr('href', originalSrc);
                                debugLog('Hauptbild #' + (index+1) + ' mit Originalbild ersetzt');
                            }
                            
                            if ($a.length > 0) {
                                $a.attr('href', originalSrc);
                                debugLog('Link für Bild #' + (index+1) + ' mit Originalbild ersetzt');
                            }
                            
                            // Stelle sicher, dass das Bild sichtbar ist
                            $(this).show();
                        }
                    }
                } else {
                    // Verstecke überschüssige Bilder
                    $(this).hide();
                    debugLog('Überschüssiges Bild #' + (index+1) + ' versteckt');
                }
            });
            
            // Ersetze die Thumbnails
            if ($galleryThumbnails.length > 0) {
                var $thumbs = $galleryThumbnails.find('li img');
                
                if ($thumbs.length > 0) {
                    debugLog('Gefunden: ' + $thumbs.length + ' Thumbnail-Bilder');
                    
                    $thumbs.each(function(index) {
                        var $thumbLi = $(this).parent();
                        
                        if (index < validImages.length) {
                            $(this).attr('src', validImages[index]);
                            $thumbLi.show();
                            debugLog('Thumbnail #' + (index+1) + ' ersetzt');
                        } else {
                            // Verstecke überschüssige Thumbnails
                            $thumbLi.hide();
                            debugLog('Überschüssiges Thumbnail #' + (index+1) + ' versteckt');
                        }
                    });
                }
            }
            
            // Trigger resize-Event, um sicherzustellen, dass die Galerie korrekt angezeigt wird
            setTimeout(function() {
                $(window).trigger('resize');
                debugLog('Resize-Event ausgelöst');
                
                // Zusätzlich FlexSlider neu initialisieren
                if ($gallery.data('flexslider')) {
                    try {
                        $gallery.flexslider('resize');
                        debugLog('FlexSlider neu initialisiert');
                    } catch (e) {
                        debugLog('Fehler beim Neu-Initialisieren des FlexSliders', e);
                    }
                }
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
                        var $a = $currentImage.find('a');
                        
                        // Original-Bilder wiederherstellen
                        $img.attr('src', original.imgSrc);
                        $img.attr('data-src', original.imgDataSrc);
                        $img.attr('data-large_image', original.imgDataLargeImage);
                        $a.attr('href', original.linkHref);
                        
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
            
            // Nur Logging in der Konsole, kein visuelles Debug-Fenster mehr
            for (var i = 0; i < images.length; i++) {
                if (images[i]) {
                    var isValidImage = images[i].indexOf('data:image') === 0;
                    var imageLength = images[i].length;
                    console.log('FPD DEBUG: Bild #' + (i+1) + (isValidImage ? ' (Gültig, Länge: ' + imageLength + ')' : ' (Ungültig)'));
                }
            }
        }
    }
});
