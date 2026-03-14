document.addEventListener('contextmenu', event => event.preventDefault());

document.onkeydown = function(e) {
    // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
    if (
        e.keyCode == 123 || 
        (e.ctrlKey && e.shiftKey && e.keyCode == 73) || 
        (e.ctrlKey && e.shiftKey && e.keyCode == 74) || 
        (e.metaKey && e.altKey && e.keyCode == 73) || // Mac Cmd+Opt+I
        (e.ctrlKey && e.keyCode == 85) || 
        (e.metaKey && e.keyCode == 85) // Mac Cmd+U
    ) {
        return false;
    }
};
