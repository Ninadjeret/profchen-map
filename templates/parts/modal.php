<?php
/**
 * MODAL
 */
?>
<dialog id="dialog" class="gym-dialog mdl-dialog">
    <div class="mdl-dialog__wrap">
        
        <div class="modal__screen" data-screen="gym">
            <h3 class="mdl-dialog__title">MDL Dialog</h3>
            <p class="mdl-dialog__city"></p>
            <hr>
            <div class="mdl-dialog__egg">
                <p><span class="annonce"></span><span class="source"></span></p>
                <img src="">
                <span class="mdl-dialog__counter"></span>
            </div>
            <hr>
            <div class="mdl-dialog__content">
                <ul>

                </ul>
            </div>
            <div class="mdl-dialog__actions">
                <button type="button" class="mdl-button"><i class="material-icons">close</i></button>
            </div>
        </div>
        
        <div class="modal__screen" data-screen="update-raid">
            <h3 class="">Préciser le Pokémon</h3>
            <hr>
            <div class="update-raid__wrapper">
                
            </div>
            <hr>
            <div class="footer-action">
                <a class="bt modal__action cancel" href="">Anuler</a>
            </div>
        </div>
        
        <div class="modal__screen" data-screen="create-raid">
            <h3 class="">Annoncer un raid</h3>
            
            <hr>
            <div class="step" data-step-num="1" data-step-name="timer">
                <p class="step__title">A quelle heure commence-t-il ?</p>
                <p class="step__timer" data-starttime=""><span class="step__timer--delai"></span><br><span class="step__timer--horaires"></span></p>
                <input type="range" class="range" min="-60" max="45" step="1" value="-60" data-orientation="horizontal">               
            </div>
            
            <hr>
            <div class="step" data-step-num="2" data-step-name="level" data-validate="oui">
                <p class="step__title">Quel est son niveau ?</p>
                <div class="step__wrapper">  
                </div>
            </div>
            
            <hr>
            <div class="step" data-step-num="3b" data-step-name="boss">
                <p class="step__title">Quel est le Pokémon ?</p>
                <div class="step__wrapper">
                </div>
            </div>
            
            <div class="footer-action">
                <a class="bt modal__action cancel" href="">Annuler</a>
            </div>
        </div>
        
    </div>
</dialog>

