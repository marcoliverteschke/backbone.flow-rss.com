<section class="items_list <?php echo isset($list_classes) ? implode(' ', $list_classes) : '' ?>">
  <article v-if="entries.length != 0" v-for="entry in entries" class="item" v-bind:data-guid="entry.item.guid">
    <h1 v-html="entry.item.title"></h1>
    <footer>
      {{ entry.item.pub_date | format_date }}
       |
      <a class="js-pjax" v-bind:href="'/feeds/' + entry.feed.id">{{ entry.feed.title }}</a>
       |
      <a class="pictogram" v-bind:href="entry.item.link" target="_blank" title="Open original article">o</a>
       |
      <a class="pictogram" data-tool="star" href="javascript:void(0);" title="Star item">*</a>
       |
      <a class="pictogram" data-tool="instapaper" v-bind:href="'http://www.instapaper.com/edit?url=' + encodeURIComponent(entry.item.link)  + '&title=' + encodeURIComponent(entry.item.title)" title="Send article to Instapaper" target="_blank">a</a>
    </footer>
  </article>
  <section v-if="entries.length == 0" class="backdrop space"><h1>A vast emptiness spreads before you…</h1></section>
</section>
