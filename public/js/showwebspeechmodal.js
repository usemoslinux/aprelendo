/**
 * Copyright (C) 2022 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    var speech = new SpeechSynthesisUtterance();
    var voices = window.speechSynthesis.getVoices();
    var firstentry = true;

    document.getElementById("ws-volume").value = speech.volume = 1; // From 0 to 1
    document.getElementById("ws-rate").value = speech.rate = 1; // From 0.1 to 10
    document.getElementById("ws-pitch").value = speech.pitch = 1; // From 0 to 2    
    speech.lang = document.documentElement.lang; // get language of ebook file

    window.speechSynthesis.getVoices().forEach(function(voice, i) {
        let voiceSelect = document.getElementById('ws-voices');
        if (voice.lang.startsWith(speech.lang)) {
            if (firstentry) {
                speech.voice = voice;
                firstentry = false;
            }

            const option = document.createElement('option');
            option.textContent = `${voice.name} (${voice.lang})`;
            option.setAttribute('data-lang', voice.lang);
            option.setAttribute('data-name', voice.name);
            voiceSelect.appendChild(option);
        }
    });
      
    document.querySelector("#ws-rate").addEventListener("input", () => {
        const rate = document.querySelector("#ws-rate").value;
        speech.rate = rate;
        document.querySelector("#ws-rate-label").innerHTML = rate;
      });
      
    document.querySelector("#ws-volume").addEventListener("input", () => {
        const volume = document.querySelector("#ws-volume").value;
        speech.volume = volume;
        document.querySelector("#ws-volume-label").innerHTML = volume;
    });
    
    document.querySelector("#ws-pitch").addEventListener("input", () => {
        const pitch = document.querySelector("#ws-pitch").value;
        speech.pitch = pitch;
        document.querySelector("#ws-pitch-label").innerHTML = pitch;
    });
    
    document.querySelector("#ws-voices").addEventListener("change", () => {
        let voiceSelect = document.getElementById("ws-voices");
        speech.voice = voices.find(x => x.name === voiceSelect.options[voiceSelect.selectedIndex].dataset.name);
    });
    
    document.querySelector("#ws-start").addEventListener("click", () => {
        speech.text = strip(document.getElementById("viewer").textContent);
        window.speechSynthesis.speak(speech);
    });
    
    document.querySelector("#ws-pause").addEventListener("click", () => {
        window.speechSynthesis.pause();
    });
    
    // document.querySelector("#ws-resume").addEventListener("click", () => {
    //     window.speechSynthesis.resume();
    // });
    
    // document.querySelector("#ws-cancel").addEventListener("click", () => {
    //     window.speechSynthesis.cancel();
    // });

    function strip(html){
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || "";
     }

});