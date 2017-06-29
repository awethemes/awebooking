<script id="tmpl-iconpicker-font-icon" type="text/html">
    <i class="_icon {{data.type}} {{ data.icon }}"></i>
</script>

<script id="tmpl-iconpicker-image-icon" type="text/html">
    <img src="{{ data.url }}" class="_icon" />
</script>

<script id="tmpl-iconpicker-svg-icon" type="text/html">
    <img src="{{ data.url }}" class="_icon" />
</script>

<script id="tmpl-iconpicker-font-item" type="text/html">
    <div class="attachment-preview js--select-attachment">
    <div class="thumbnail">
    <span class="_icon"><i class="{{data.type}} {{ data.id }}"></i></span>
    <div class="filename"><div>{{ data.name }}</div></div>
    </div>
    </div>
    <a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a>
</script>

<script id="tmpl-iconpicker-svg-item" type="text/html">
    <div class="attachment-preview js--select-attachment svg-icon">
    <div class="thumbnail">
    <div class="centered">
    <img src="{{ data.url }}" alt="{{ data.alt }}" class="_icon _{{data.type}}" />
    </div>
    </div>
    </div>
    <a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a>
</script>
