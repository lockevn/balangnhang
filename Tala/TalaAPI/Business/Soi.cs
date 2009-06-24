using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using System.Collections.Generic;

using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;


namespace TalaAPI.Business
{
    public class Soi : APIDataEntry
    {
        int _Id;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int Id
        {
            get { return _Id; }
            set { _Id = value; }
        }

        string _Name;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string Name
        {
            get { return _Name; }
            set { _Name = value; }
        }
        
        string _OwnerUsername;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string OwnerUsername
        {
            get
            {
                return _OwnerUsername;
            }
            set { _OwnerUsername = value; }
        }

        public DateTime StartTime { get; set; }
        public DateTime EndTime { get; set; }
        public string Description { get; set; }
       
                
        /// <summary>
        /// số tiền đang ở trong Gà, tính bằng Chip
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int GaValue { get; set; }

        bool _IsLocked = false;
        /// <summary>
        /// Nếu trường này bằng true thì sới này không thay đổi được luật hay option nữa.
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
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
        
        bool _IsPlaying = false;
        /// <summary>
        /// Nếu trường này bằng true, thì sới này đang có ván đang chơi, ván đang diễn ra. Nếu trường này bằng false, ván chưa bắt đầu hoặc đã kết thúc. Lúc này client cần đọc thông tin về kết quả ván chơi trước , ...
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
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





        List<Seat> _SeatList;
        /// <summary>
        /// Danh sách các chỗ ngồi chơi trong sới
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Seat> SeatList
        {
            get { return _SeatList; }
            set { _SeatList = value; }
        }

        Option _SoiOption;
        /// <summary>
        /// Các tuỳ chọn của Sới này, setup khi bắt đầu sới mới
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public Option SoiOption
        {
            get { return _SoiOption; }
            set { _SoiOption = value; }
        }

        Van _CurrentVan;
        /// <summary>
        /// Ván đang chơi của Sới hiện tại
        /// </summary>
        public Van CurrentVan
        {
            get { return _CurrentVan; }
            set { _CurrentVan = value; }
        }





        /// <summary>
        /// Khởi tạo đối tượng sới, cho các Player gia nhập
        /// </summary>
        /// <param name="id"></param>
        /// <param name="name"></param>
        /// <param name="owner"></param>
        public Soi(int id, string name, string owner)
        {
            this.Id = id;
            this.Name = name;
            this.OwnerUsername = owner;
            this.StartTime = System.DateTime.Now;
            this.SeatList = new List<Seat>();
            this.SoiOption = new Option();

            // ván sẽ được khởi tạo sau
        }


        /// <summary>
        /// hệ thống tạo ván mới, tự chia bài, tự xếp chỗ nếu truyền tham số
        /// </summary>
        /// <param name="isXepChoRequired">truyền true để xếp lại random chỗ</param>
        /// <returns>đối tượng ván vừa tạo</returns>
        internal Van CreateVan(bool isXepChoRequired)
        {
            int newVanIndex = 1;
            /// index van moi = index van cu + 1
            if (this._CurrentVan != null)
            {
                newVanIndex = this._CurrentVan.Index++;
            }

            Van oldVan = this._CurrentVan;
            Van newVan = new Van(newVanIndex, this);
            this._CurrentVan = newVan;

            /// xep cho randomly
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /// Chia bài, chia cho người thắng ván cũ trước (nếu có)
            newVan.ChiaBai(oldVan == null ? "" : oldVan.WinnerUsername);

            /// reset HaIndex
            Seat tmpSeat = this.SeatList[newVan.CurrentTurnSeatIndex];
            for (int i = 0; i < this.SeatList.Count; i++)
            {
                tmpSeat.HaIndex = i;
                int nextIndex = Seat.GetNextSeatIndex(tmpSeat.Pos, this.SeatList.Count);
                tmpSeat = this.SeatList[nextIndex];
            }

            return newVan;
        }





        #region Thêm bớt player

        /// <summary>
        /// Tạo một Seat mới trong sới, Ấn user vào seat, tự động đặt owner
        /// </summary>
        /// <param name="player"></param>
        /// <returns> -3 đã join sới khác rồi, -2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ ,. Trả về số >= 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>        
        protected int AddPlayer(User player)
        {
            if (player == null)
            {
                // không tìm được online user tương ứng
                return -2;
            }

            Seat seatDangNgoiTrongSoi = this.GetSeatByUsername(player.Username);
            if (seatDangNgoiTrongSoi == null)
            {
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
                else
                {
                    // chưa ngồi thì cho vào ngồi
                    Seat newSeat = new Seat(-1, player);
                    this.SeatList.Add(newSeat);
                    this.ReIndexSeatList();
                    player.CurrentSoi = this;  // this sới

                    if (this.SeatList.Count == 1)
                    {
                        // người đầu tiên vào
                        _OwnerUsername = player.Username;
                    }

                    return newSeat.Pos;
                }
            }
            else
            {
                // ngồi rồi thì trả ra index chỗ đang ngồi
                return seatDangNgoiTrongSoi.Pos;
            }
        }

        /// <summary>
        /// Tạo một Seat mới trong sới, Ấn user vào seat, tự động đặt owner
        /// </summary>
        /// <param name="username"></param>
        /// <returns> -3 đã join sới khác rồi, -2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ ,. Trả về số >= 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>
        public int AddPlayer(string username)
        {
            User player = Song.Instance.GetUserByUsername(username);
            return AddPlayer(player);
        }


        /// <summary>
        /// Bỏ user ra khỏi danh sách người chơi hiện tại, gỡ bỏ chỗ ngồi
        /// </summary>
        /// <param name="player"></param>
        /// <returns>-2 nếu không đuổi cổ thành công (vì lý do nào đó, player rỗng ...), -1 nếu user vốn không nằm trong sới, >=0 nếu đuổi cổ thành công</returns>
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
                return -1;
            }

            Seat seatDangNgoi = GetSeatByUsername(player.Username);
            if (seatDangNgoi == null)
            {
                // không tìm thấy user này đang ngồi trong sới
                return -1;
            }
            else
            {
                // đưa ra khỏi sới
                this.SeatList.Remove(seatDangNgoi);
                this.ReIndexSeatList();
                player.CurrentSoi = null;

                if (this.SeatList.Count == 0)
                {
                    // không còn ai, xoá tên owner
                    _OwnerUsername = string.Empty;
                }
                else if (this.OwnerUsername == player.Username)
                {
                    // owner rời sới, chuyển owner cho người tiếp theo
                    _OwnerUsername = this.SeatList[0].Player.Username;
                }

                return 0;
            }
        }

        /// <summary>
        /// Bỏ user ra khỏi danh sách người chơi trong sới hiện tại
        /// </summary>
        /// <param name="username"></param>
        /// <returns>-2 nếu không đuổi cổ thành công (vì lý do nào đó, player rỗng ...), -1 nếu user vốn không nằm trong sới, >=0 nếu đuổi cổ thành công</returns>
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
            if (GetSeatByUsername(username) != null)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /// <summary>
        /// Lặp qua các Seat trong sới, nếu Player của Seat đó có username trùng với đối số thì trả ra Seat đó, nếu không thì trả về null
        /// </summary>
        /// <param name="username"></param>
        /// <returns></returns>
        public Seat GetSeatByUsername(string username)
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

        /// <summary>
        /// Lấy Seat đang có lượt
        /// </summary>
        /// <returns></returns>
        public Seat GetSeatOfCurrentInTurn()
        {
            Seat seatRet = this._SeatList[this._CurrentVan.CurrentTurnSeatIndex];
            return seatRet;
        }

        /// <summary>
        /// Tìm seat theo hạIndex 
        /// </summary>
        /// <param name="haIndex"></param>
        /// <returns>seat hoặc null nếu không tìm thấy</returns>
        public Seat GetSeatByHaIndex(int haIndex)
        {
            foreach (Seat seat in this.SeatList)
            {
                if (seat.HaIndex == haIndex)
                {
                    return seat;
                }
            }
            return null;
        }



        /// <summary>
        /// Xếp lại chỗ Random các vị trí trong Sới
        /// </summary>
        public void XepChoRandom()
        {
            int max = this.SeatList.Count;
            if (this.SeatList == null || max == 0)
            {
                return;
            }

            /// generate a temp Arr of max elements
            int[] tmpArr = new int[max];
            for (int i = 0; i < max; i++)
            {
                tmpArr[i] = i;
            }

            /// randomly reindexing tmpArr
            int[] randomArr = TextUtil.ReindexArrayRandomly(tmpArr);


            /*create new player list*/
            User[] newPlayerArr = new User[max];
            /*pick player from SeatList according to randomArr and add to newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                newPlayerArr[i] = (this.SeatList.ElementAt(randomArr[i]) as Seat).Player;
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
        /// <returns>-1 nếu mọi người chưa sẵn sàng, -2 nếu sới đang chơi, -3 số người chơi chưa đủ (hiện tại là 4). 1 nếu OK</returns>
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


            if (4 < this.SeatList.Count || this.SeatList.Count < 2)
            {
                return -3;
            }



            // COME HERE MEAN all Condition is OK

            // bật cờ đang chơi
            _IsPlaying = true;
            //o	Bắt đầu ván với các lựa chọn của Sới hiện tại
            //o	Hệ thống sẽ tạo ván mới, tự chia bài

            CreateVan(false);
            return 0;
        }

               
        /// <summary>
        /// đặt cờ ready tại Seat của user
        /// </summary>
        /// <param name="user">user cần giương cờ ready</param>
        /// <returns>Seat của user đó nếu Seat đã có cờ ready OK, nếu fail thì trả về null</returns>
        internal Seat SetReady(User user)
        {
            // đặt cờ ready
            Seat seatOfUser = GetSeatByUsername(user.Username);
            if (null != seatOfUser)
            {
                seatOfUser.IsReady = true;
            }           

            return seatOfUser;
        }

        /// <summary>
        /// Quét qua các Seat, xem toàn bộ người chơi trong ván đã ready chưa? Nếu trả về true, có thể gọi StartPlaying được
        /// </summary>
        /// <returns></returns>
        public bool IsAllPlayerReady()
        {
            bool bAllPlayerReady = true;
            foreach (Seat seat in SeatList)
            {
                bAllPlayerReady = bAllPlayerReady && seat.IsReady;
            }
            return bAllPlayerReady;
        }


        /// <summary>
        /// sắp xếp lại index của các chỗ ngồi trong mảng SeatList. 
        /// Hàm này cần phải gọi mỗi khi có sự thay đổi vè chỗ ngồi trong  Sới (thêm bớt player)
        /// Hàm sẽ gán Pos của mỗi Seat = chính index của Seat đó trong SeatList của sới
        /// </summary>
        internal void ReIndexSeatList()
        {
            foreach (Seat seat in SeatList)
            {
                seat.Pos = SeatList.IndexOf(seat);
                seat.HaIndex = seat.Pos;
            }
        }


        



        /// <summary>
        /// Nộp chip vào Gà, lấy từ túi User phải nộp. Số chip cần nộp lấy trong thông số của Sới
        /// </summary>
        /// <param name="userPhaiNop">Lấy từ túi người này để nộp</param>
        /// <returns>Số tiền đã nộp</returns>
        public int NopGa(User userPhaiNop)
        {
            int nTienPhat = Cashier.CHIP_NOP_GA * this.SoiOption.TiGiaChip;
            // cộng gà lên 1 chip
            this.GaValue += nTienPhat;

            // trừ tiền trong túi user đi
            userPhaiNop.SubtractMoney(nTienPhat);

            return nTienPhat;
        }
    
    }
}
