<template>
  <NavBar :name="loggedInUser" />
  <div class="about-container">
    <div class="about-header">
      <h1>Tim Kami</h1>
      <p class="subtitle">Bertemu dengan para profesional di balik secangkir kopi sempurna Anda</p>
    </div>

    <div class="team-grid">
      <div v-for="member in teamMembers" 
           :key="member.id" 
           class="team-card"
           :class="{ 'active': member.isActive }"
           @mouseenter="member.isActive = true"
           @mouseleave="member.isActive = false">
        <div class="card-inner">
          <div class="card-front">
            <div class="member-image">
              <img :src="member.image" :alt="member.name">
            </div>
            <div class="member-info">
              <h3>{{ member.name }}</h3>
              <p class="position">{{ member.position }}</p>
            </div>
          </div>
          
          <div class="card-back">
            <h3>{{ member.name }}</h3>
            <p class="position">{{ member.position }}</p>
            <p class="bio">{{ member.bio }}</p>
            <div class="member-stats">
              <div class="stat">
                <i class="fas fa-medal"></i>
                <span>{{ member.specialty }}</span>
                <p>Spesialisasi</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="about-section">
      <h2>Tentang Kopi Kita</h2>
      <p class="about-text">
        Sejak 2024, Kopi Kita telah berkomitmen untuk menghadirkan pengalaman kopi terbaik untuk para pecinta kopi.
        Dengan tim yang berpengalaman dan berdedikasi, kami memastikan setiap cangkir kopi yang kami sajikan
        memenuhi standar kualitas tertinggi.
      </p>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';

export default {
  components: {
    NavBar
  },
  data() {
    return {
      teamMembers: [
        {
          id: 1,
          name: "Rahmatullah Akbar Prima",
          position: "Front End Developer",
          image: "akbar.JPG", // Replace with actual image path
          bio: "Mahasiswa Informatika Semester 5 yang sedang belajar mengenai Frontend Development.",
          specialty: "Frontend Development",
          isActive: false
        },
        {
          id: 2,
          name: "Hisyam Bima Ekhsantama",
          position: "Back End Developer",
          image: "hisyam.JPG", // Replace with actual image path
          bio: "Mahasiswa Informatika Semester 5 yang sedang belajar mengenai Backend Development dan Network Security.",
          specialty: "Backend Development",
          isActive: false
        },
        {
          id: 3,
          name: "Bima Bhakti Brawijaya",
          position: "UI/UX Designer",
          image: "bima.JPG", // Replace with actual image path
          bio: "Mahasiswa Informatika Semester 5 yang sedang belajar mengenai UI/UX Design.",
          specialty: "UI/UX Design",
          isActive: false
        },
        {
          id: 4,
          name: "M. Haidar Afif Al Azizi",
          position: "Full Stack Developer",
          image: "afif.JPG", // Replace with actual image path
          bio: "Mahasiswa Informatika Semester 5 yang sedang belajar mengenai Full Stack Development.",
          specialty: "Full Stack Development",
          isActive: false
        }
      ]
    };
  }
};
</script>

<style scoped>
.about-container {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 4rem 2rem;
}

.about-header {
  text-align: center;
  margin-bottom: 4rem;
}

.about-header h1 {
  color: #6F4E37;
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.subtitle {
  color: #666;
  font-size: 1.2rem;
  max-width: 600px;
  margin: 0 auto;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.team-card {
  background: transparent;
  perspective: 1000px;
  height: 400px;
}

.card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform 0.8s;
}

.team-card.active .card-inner {
  transform: rotateY(180deg);
}

.card-front, .card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-front {
  background: white;
}

.card-back {
  background: #6F4E37;
  color: white;
  transform: rotateY(180deg);
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.member-image {
  width: 100%;
  height: 300px;
  overflow: hidden;
}

.member-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.team-card:hover .member-image img {
  transform: scale(1.05);
}

.member-info {
  padding: 1.5rem;
  text-align: center;
}

.member-info h3, .card-back h3 {
  margin: 0;
  font-size: 1.4rem;
  color: #2c3e50;
}

.card-back h3 {
  color: white;
}

.position {
  color: #666;
  font-size: 1rem;
  margin: 0.5rem 0;
}

.card-back .position {
  color: rgba(255,255,255,0.8);
}

.bio {
  margin: 1rem 0;
  font-size: 0.9rem;
  line-height: 1.6;
  color: rgba(255,255,255,0.9);
}

.member-stats {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 1rem;
}

.stat {
  text-align: center;
}

.stat i {
  font-size: 1.5rem;
  color: rgba(255,255,255,0.9);
  margin-bottom: 0.5rem;
}

.stat span {
  display: block;
  font-size: 1.2rem;
  font-weight: bold;
  color: white;
}

.stat p {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.7);
  margin: 0.25rem 0 0;
}

.about-section {
  max-width: 800px;
  margin: 4rem auto 0;
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.about-section h2 {
  color: #6F4E37;
  margin-bottom: 1rem;
}

.about-text {
  color: #666;
  line-height: 1.8;
}

@media (max-width: 768px) {
  .about-container {
    padding: 2rem 1rem;
  }

  .team-grid {
    gap: 1.5rem;
  }

  .about-header h1 {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }
}

</style>