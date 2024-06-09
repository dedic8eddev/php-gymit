<section class="hpCalendar">
  <div class="container">
    <h2 class="sectionTitle">Skupinové lekce <span class="smallText">S profesionálními lektory a vybavením</span></h2>
    <section class="calendarSection oneDay">
      <?php $this->load->view('frontend/calendar/calendar',$calendar); ?>
    </section>
    <div class="txt-center"><a href="/calendar" class="btn btn--transparent">
        <div class="bg"></div><span><?php echo $page_homepage['lessons_btn_text']; ?></span>
      </a></div>
  </div>
</section>