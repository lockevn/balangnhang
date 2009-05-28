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
using TalaAPI.XMLRenderOutput;



namespace TalaAPI.Business
{
    public class Soi : APIDataEntry
    {
         string _Name;
         public string Name
         {
             get { return _Name; }
             set { _Name = value; }
         }

         int _Id;
         public int Id
         {
             get { return _Id; }
             set { _Id = value; }
         }

        public DateTime StartTime;
        public DateTime EndTime;
        public string Description;
        
        List<Seat> _SeatList;
        public List<Seat> SeatList
        {
            get { return _SeatList; }
            set { _SeatList = value; }
        }
        
        string _OwnerUsername;
        public string OwnerUsername
        {
            get { return _OwnerUsername;  }
            set { _OwnerUsername = value; }
        }

        public Option SoiOption;

        
        /// <summary>
        /// tính bằng Chip
        /// </summary>
        int _GaValue;
        /// <summary>
        /// tính bằng Chip
        /// </summary>
        public int GaValue
        {
            get { return _GaValue; }
            set { _GaValue = value; }
        }

        Van _CurrentVan;
        public Van CurrentVan
        {
            get { return _CurrentVan; }
            set { _CurrentVan = value; }
        }


        #region Cờ báo trạng thái        

        bool _IsLocked;
        /// <summary>
        /// Nếu trường này bằng true thì sới này không thay đổi được luật hay option nữa.
        /// </summary>
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


        bool _IsPlaying;
        /// <summary>
        /// Nếu trường này bằng true, thì sới này đang có ván đang chơi, ván đang diễn ra. Nếu trường này bằng false, ván chưa bắt đầu hoặc đã kết thúc. Lúc này client cần đọc thông tin về kết quả ván chơi trước , ...
        /// </summary>
        public bool IsPlaying
        {
            get
            {
                return _IsPlaying;
            }
            set
            {
                _IsPlaying = value;
            }
        }

        

        #endregion




        public Soi(int id, string name, string owner)
        {
            this.Id = id;
            this.Name = name;
            this.OwnerUsername = owner;
            this.StartTime = System.DateTime.Now;
            this.SeatList = new List<Seat>();
        }

        
        /// <summary>
        /// hệ thống tạo ván mới, tự chia bài, tự xếp chỗ nếu truyền tham số
        /// </summary>
        /// <param name="isXepChoRequired">truyền true để xếp lại random chỗ</param>
        /// <returns>đối tượng ván vừa tạo</returns>
        internal Van CreateVan(bool isXepChoRequired)
        {

            int newVanIndex = 1;
            /*index van moi = index van cu + 1*/
            if (this._CurrentVan != null)
            {
                newVanIndex = this._CurrentVan.Index++;
            }
            Van newVan = new Van(newVanIndex, this);
            this._CurrentVan = newVan;

            /*xep cho randomly*/
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /*chia bai*/
            newVan.ChiaBai();

            return newVan;
        }


        /// <summary>
        /// Nộp chip vào Gà, lấy từ túi User phải nộp. Số chip cần nộp lấy trong thông số của Sới
        /// </summary>
        /// <param name="userPhaiNop">Lấy từ túi người này để nộp</param>
        /// <returns>Số tiền đã nộp</returns>
        public int NopGa(User userPhaiNop)
        {
            const int TienPhat = 1;
            // cộng gà lên 1 chip
            this.GaValue += TienPhat;

            // trừ tiền trong túi user đi
            userPhaiNop.SubtractMoney(this.SoiOption.TiGiaChip * TienPhat);
            
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
                return -3;
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
        /// <param name="username"></param>
        /// <returns> -3 đã join sới khác rồi, -2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ ,. Trả về số > 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>
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


        /// <summary>
        /// 
        /// </summary>
        /// <returns>-1 nếu mọi người chưa sẵn sàng, -2 nếu sới đang chơi, đã bắt đầu chơi rồi. 1 nếu OK</returns>
        internal int StartPlaying()
        {
            if (_IsPlaying)
            {
                return -2;
            }

            if (IsAllPlayerReady() == false)
            {
                //o	Nếu có player nào chưa ready, lỗi, id=PLAYER_NOT_READY, trong info sẽ có username chưa ready đó
                return -1;
            }
            
            // bật cờ đang chơi
            _IsPlaying = true;
            //o	Bắt đầu ván với các lựa chọn của Sới hiện tại
            //o	Hệ thống sẽ tạo ván mới, tự chia bài
            Van van = CreateVan(false);
            

            return 0;
        }

        /// <summary>        
        /// đặt cờ ready tại Seat của user        
        /// </summary>
        /// <param name="user">user giương cờ ready</param>
        internal void SetReady(User user)
        {
            // đặt cờ ready
            GetSeatOfUserInSoi(user.Username).IsReady = true;

            // thử gọi hàm StartPlaying (trong đấy tự nó kiểm tra điều kiện để bắt đầu ván)
            try
            {
                StartPlaying();
            }
            catch { }
        }

        /// <summary>
        /// Toàn bộ người chơi trong ván đã ready chưa? Nếu trả về true, có thể gọi StartPlaying được
        /// </summary>
        /// <returns></returns>
        private bool IsAllPlayerReady()
        {
            bool bAllPlayerReady = true;
            foreach (Seat seat in SeatList)
            {
                bAllPlayerReady = bAllPlayerReady && seat.IsReady;
            }
            return bAllPlayerReady;
        }
          
    }
}
