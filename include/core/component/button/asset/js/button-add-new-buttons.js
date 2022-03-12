/**
 * @name Button Image - Add New Buttons
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {

        console.log( 'aalButtonAddNew', aalButtonAddNew );

        var _oAddNewButton      = $( '.page-title-action' ).first();

        // Generate Default Buttons
        var _oGenerateDefaultsButton = _oAddNewButton.clone().text( aalButtonAddNew.labels.generateDefaults );
        _oGenerateDefaultsButton.attr( 'href', aalButtonAddNew.URLs.generateDefaults );
        _oGenerateDefaultsButton.insertAfter( _oAddNewButton );

        // Add Classic Button
        var _oAddNewButtonClassic = _oAddNewButton.clone().text( aalButtonAddNew.labels.addClassic );
        _oAddNewButtonClassic.attr( 'href', aalButtonAddNew.URLs.addClassic );
        _oAddNewButtonClassic.insertAfter( _oAddNewButton );

        // Add Image Button
        var _oAddNewButtonImage = _oAddNewButton.clone().text( aalButtonAddNew.labels.addImage );
        _oAddNewButtonImage.attr( 'href', aalButtonAddNew.URLs.addImage );
        _oAddNewButtonImage.insertAfter( _oAddNewButton );

    });


}(jQuery));