using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;
using TalaAPI.Lib;
using System.Collections.Generic;



namespace TalaAPI.Business
{
    public class Soi
    {
        public string Name;
        public int Id;
        public DateTime StartTime;
        public DateTime EndTime;
        public string Description;
        public List<Seat> SeatList;
        
        string _OwnerUsername;
        public string OwnerUsername
        {
            get { return _OwnerUsername;  }
            set { _OwnerUsername = value; }
        }

        public Option SoiOption;

        public int GaValue;
        public Van CurrVan;
        
        bool _IsLocked;
        public bool IsLocked
        {
            get
            {
                return _IsLocked;
            }
            set
            {
                _IsLocked = value;
            }
        }





        public Soi(int id, string owner)
        {
            this.Id = id;
            this.OwnerUsername = owner;
            this.StartTime = System.DateTime.Now;
            this.SeatList = new List<Seat>();
        }

        
        public Van CreateVan(bool isXepChoRequired)
        {

            int newVanIndex = 1;
            /*index van moi = index van cu + 1*/
            if (this.CurrVan != null)
            {
                newVanIndex = this.CurrVan.Index++;
            }
            Van newVan = new Van(newVanIndex, this);
            this.CurrVan = newVan;

            /*xep cho randomly*/
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /*chia bai*/
            newVan.ChiaBai();
            return newVan;

        }

        public int NopGa(int value)
        {
            if (value <= 0)
            {
                return -1;
            }
            this.GaValue += value;
            return this.GaValue;
        }



        #region Thêm bớt player

        
        protected int AddPlayer(User player)
        {
            if (player == null)
            {
                // không tìm được online user tương ứng
                return -2;
            }

            int count = this.SeatList.Count;
            if (count >= 4)
            {
                // sới đầy rồi, không cho vào
                return -1;
            }

            if (player.CurrentSoi != null)
            {
                // vào sới khác rồi còn lớ xớ ở đây làm gì
                return -1;
            }


            Seat seatDangNgoiTrongSoi = this.GetSeatOfUserInSoi(player.Username);
            if (seatDangNgoiTrongSoi == null)
            {
                // chưa ngồi thì cho vào ngồi
                Seat newSeat = new Seat(SeatList.Count, player);
                this.SeatList.Add(newSeat);
                player.CurrentSoi = this;  // this sới
                return newSeat.Index;
            }
            else
            {
                // ngồi rồi thì trả ra index chỗ đang ngồi
                return seatDangNgoiTrongSoi.Index;
            }
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="player"></param>
        /// <returns>-2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ hoặc đã join sới khác rồi. Trả về số > 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>
        public int AddPlayer(string username)
        {
            User player = Song.Instance.GetUserByUsername(username);
            return AddPlayer(player);            
        }



        protected int RemovePlayer(User player)
        {
            // không tìm thấy user này đang online
            if (player == null)
            {
                return -2;
            }

            // sới chả còn ai            
            if (this.SeatList.Count <= 0)
            {
                return 0;
            }
            
            Seat seatDangNgoi = GetSeatOfUserInSoi(player.Username);
            if (seatDangNgoi == null)
            {
                // không tìm thấy user này đang ngồi trong sới
                return 0;
            }
            else
            {
                // đưa ra khỏi sới
                this.SeatList.Remove(seatDangNgoi);
                player.CurrentSoi = null;
                return 1;
            }
        }

        /// <summary>
        /// Bỏ user ra khỏi danh sách người chơi trong sới hiện tại
        /// </summary>
        /// <param name="player"></param>
        /// <returns>-2 nếu không đuổi cổ thành công (vì lý do nào đó, player rỗng ...), 0 nếu user vốn không nằm trong sới, 1 nếu đuổi cổ thành công</returns>
        public int RemovePlayer(string username)
        {
            User player = Song.Instance.GetUserByUsername(username);
            return RemovePlayer(player);
        }
        
        
        #endregion




        /// <summary>
        /// Lặp qua các Seat trong sới, nếu username đang ở trong sới, trả về true
        /// </summary>
        /// <param name="username"></param>
        /// <returns></returns>
        public bool IsUserInSoi(string username)
        {
            if (GetSeatOfUserInSoi(username) != null)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /// <summary>
        /// Lặp qua các Seat trong sới, nếu Player của Seat đó có username trùng với đối số thì trả ra Seat đó
        /// </summary>
        /// <param name="username"></param>
        /// <returns></returns>
        public Seat GetSeatOfUserInSoi(string username)
        {
            username = username.ToStringSafetyNormalize();
            foreach (Seat seat in this.SeatList)
            {
                if (seat.Player.Username == username)
                {
                    return seat;
                }
            }
            return null;
        }

        public void XepChoRandom()
        {
            int max = this.SeatList.Count;
            if (this.SeatList == null || max == 0)
            {
                return;
            }

            /*generate a temp Arr of max elements*/
            int[] tmpArr = new int[max];
            for (int i = 0; i < max; i++)
            {
                tmpArr[i] = i;
            }

            /*randomly reindexing tmpArr*/
            int[] randomArr = TextUtil.ReindexArrayRandomly(tmpArr);


            /*create new player list*/
            User[] newPlayerArr = new User[max];
            /*pick player from SeatList according to randomArr and add to newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                newPlayerArr[i] = this.SeatList.ElementAt(randomArr[i]).Player;
            }

            /*xep lai SeatList theo newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                Seat seat = new Seat(i, newPlayerArr[i]);
                this.SeatList.Insert(i, seat);
            }
        }

        
        












    }
}
